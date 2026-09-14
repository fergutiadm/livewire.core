<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class MakeModule extends Command
{
    protected $signature = 'make:module
                            {module}
                            {entity}
                            {--theme=}
                            {--media}
                            {--attributes}
                            {--crud}
                            {--force}';

    protected $description = 'CQRS Module Generator (CORE)';

    protected int $created = 0;

    protected int $skipped = 0;

    protected string $checksumFile;

    public function handle(): void
    {
        $this->checksumFile = storage_path(
            'app/module-generator/checksums.json'
        );

        File::ensureDirectoryExists(
            dirname($this->checksumFile)
        );

        /*
        |--------------------------------------------------------------------------
        | NORMALIZATION LAYER
        |--------------------------------------------------------------------------
        |
        | No usar Str::singular().
        |
        | La entidad debe ser suministrada en singular:
        |
        | Producto
        | Categoria
        | Local
        | Moneda
        |
        */

        $module = Str::studly(
            $this->argument('module')
        );

        $entity = Str::studly(
            $this->argument('entity')
        );

        if ($module === '') {
            throw new \InvalidArgumentException(
                'El módulo no puede estar vacío.'
            );
        }

        if ($entity === '') {
            throw new \InvalidArgumentException(
                'La entidad no puede estar vacía.'
            );
        }

        $theme = $this->option('theme') ?: 'admin';

        $themePath = resource_path(
            "views/components/ui/themes/{$theme}"
        );

        if (!File::isDirectory($themePath)) {
            throw new \InvalidArgumentException(
                "Theme no encontrado: {$theme}"
            );
        }
        /*
        |--------------------------------------------------------------------------
        | DERIVED VALUES
        |--------------------------------------------------------------------------
        */

        $moduleLower = Str::kebab($module);

        $entityLower = Str::kebab($entity);

        /*
        |--------------------------------------------------------------------------
        | MODEL
        |--------------------------------------------------------------------------
        */

        $modelClass = $this->resolveModelClass(
            $entity
        );

        $dtoFields = $this->resolveDTOFields(
            $modelClass
        );

        /*
        |--------------------------------------------------------------------------
        | BASE PATH
        |--------------------------------------------------------------------------
        */

        $basePath = app_path(
            "Modules/{$module}/{$entity}"
        );

        /*
        |--------------------------------------------------------------------------
        | NAMESPACES
        |--------------------------------------------------------------------------
        */

        $moduleNamespace =
            "App\\Modules\\{$module}\\{$entity}";

        $livewireNamespace =
            "{$moduleNamespace}\\Livewire";

        $actionsNamespace =
            "{$moduleNamespace}\\Actions";

        $dtoNamespace =
            "{$moduleNamespace}\\DTOs";

        $tablesNamespace =
            "{$moduleNamespace}\\Tables";

        $servicesNamespace =
            "{$moduleNamespace}\\Services";

        $policiesNamespace =
            "{$moduleNamespace}\\Policies";

        /*
        |--------------------------------------------------------------------------
        | SHARED STUB DATA
        |--------------------------------------------------------------------------
        */

        $stubData = [

            'module' => $module,

            'moduleLower' => $moduleLower,

            'entity' => $entity,

            'entityLower' => $entityLower,

            'entityPlural' => Str::plural($entity),

            'entityPluralLower' => Str::kebab(
                Str::plural($entity)
            ),

            'theme' => $theme,

            'modelClass' => $modelClass,

            'modelBaseName' => class_basename($modelClass),

            'dtoFields' => $dtoFields,

            'dtoPropertiesCreate' =>
                $this->dtoProperties($dtoFields),

            'dtoPropertiesUpdate' =>
                $this->dtoProperties($dtoFields),

            'dtoFromArrayCreate' =>
                $this->dtoFromArray($dtoFields),

            'dtoFromArrayUpdate' =>
                $this->dtoFromArray($dtoFields),

            'dtoAttributesCreate' =>
                $this->dtoAttributes($dtoFields),

            'dtoAttributesUpdate' =>
                $this->dtoAttributes($dtoFields),

            'moduleNamespace' =>
                $moduleNamespace,

            'livewireNamespace' =>
                $livewireNamespace,

            'actionsNamespace' =>
                $actionsNamespace,

            'dtoNamespace' =>
                $dtoNamespace,

            'tablesNamespace' =>
                $tablesNamespace,

            'servicesNamespace' =>
                $servicesNamespace,

            'policiesNamespace' =>
                $policiesNamespace,
        ];

        /*
        |--------------------------------------------------------------------------
        | DIRECTORY STRUCTURE
        |--------------------------------------------------------------------------
        */

        $this->makeDirs(
            $basePath
        );

        /*
        |--------------------------------------------------------------------------
        | LIVEWIRE
        |--------------------------------------------------------------------------
        */

        $this->generateLivewire(
            $basePath,
            $stubData
        );

        /*
        |--------------------------------------------------------------------------
        | TABLES
        |--------------------------------------------------------------------------
        */

        $this->generateTables(
            $basePath,
            $stubData
        );

        /*
        |--------------------------------------------------------------------------
        | CORE
        |--------------------------------------------------------------------------
        */

        $this->generateCore(
            $basePath,
            $stubData
        );

        /*
        |--------------------------------------------------------------------------
        | DTOS
        |--------------------------------------------------------------------------
        */

        $this->generateDTOs(
            $basePath,
            $stubData
        );

        /*
        |--------------------------------------------------------------------------
        | ACTIONS
        |--------------------------------------------------------------------------
        */

        if ($this->option('crud')) {

            $this->generateActions(
                $basePath,
                $stubData
            );
        }

        /*
        |--------------------------------------------------------------------------
        | OPTIONAL FEATURES
        |--------------------------------------------------------------------------
        */

        $this->generateOptionalLivewire(
            $basePath,
            $stubData
        );

        /*
        |--------------------------------------------------------------------------
        | VIEWS
        |--------------------------------------------------------------------------
        */

        $this->generateViews(
            $stubData
        );

        /*
        |--------------------------------------------------------------------------
        | SUMMARY
        |--------------------------------------------------------------------------
        */

        $this->printSummary(
            $module,
            $entity
        );
    }

    /*
    |--------------------------------------------------------------------------
    | MODEL RESOLVER
    |--------------------------------------------------------------------------
    */

    protected function resolveModelClass(
        string $entity
    ): string {

        $class =
            "App\\Models\\{$entity}";

        if (!class_exists($class)) {

            throw new \Exception(
                "Model not found: {$class}"
            );
        }

        return $class;
    }

    protected function resolveDTOFields(
        string $modelClass
    ): array {

        $model = app($modelClass);

        $table = $model->getTable();

        $fillable = $model->getFillable();

        if ($fillable === []) {

            throw new \Exception(
                "Model {$modelClass} no tiene atributos fillable definidos."
            );
        }

        $columns = Schema::getColumns($table);

        $columnMetadata = collect($columns)
            ->keyBy('name');

        $casts = $model->getCasts();

        $fields = [];

        foreach ($fillable as $name) {

            if (!isset($columnMetadata[$name])) {
                continue;
            }

            $column = $columnMetadata[$name];

            $fields[] = [

                'name' => $name,

                'type' => $this->resolveDTOType(
                    $name,
                    $column['type_name']
                        ?? $column['type']
                        ?? null,
                    $casts[$name] ?? null
                ),

                'nullable' => (bool) (
                    $column['nullable'] ?? false
                ),

                'default' => $column['default'] ?? null,
            ];
        }

        return $fields;
    }

    protected function resolveDTOType(
        string $name,
        ?string $columnType,
        ?string $cast
    ): string {

        if ($cast) {

            return match ($cast) {

                'integer',
                'int' => 'int',

                'float',
                'double',
                'decimal' => 'float',

                'boolean',
                'bool' => 'bool',

                'string' => 'string',

                default =>
                    $this->resolveDTOTypeFromName(
                        $name,
                        $columnType
                    ),
            };
        }

        return $this->resolveDTOTypeFromName(
            $name,
            $columnType
        );
    }

    protected function resolveDTOTypeFromName(
        string $name,
        ?string $columnType
    ): string {

        if (
            $name === 'id' ||
            Str::endsWith($name, '_id')
        ) {
            return 'int';
        }

        return match (
            strtolower((string) $columnType)
        ) {

            'tinyint',
            'smallint',
            'mediumint',
            'int',
            'integer',
            'bigint' => 'int',

            'decimal',
            'numeric',
            'float',
            'double',
            'real' => 'float',

            'boolean',
            'bool' => 'bool',

            default => 'string',
        };
    }

    /*
    |--------------------------------------------------------------------------
    | DTO PROPERTIES
    |--------------------------------------------------------------------------
    */

    protected function dtoProperties(
        array $fields
    ): string {

        $lines = [];

        foreach ($fields as $field) {

            if ($field['name'] === 'id') {
                continue;
            }

            $nullable = $field['nullable']
                ? '?'
                : '';

            $lines[] = sprintf(
                '        public readonly %s%s $%s,',
                $nullable,
                $field['type'],
                $field['name']
            );
        }

        return implode("\n", $lines);
    }

    /*
    |--------------------------------------------------------------------------
    | DTO FROM ARRAY
    |--------------------------------------------------------------------------
    */

    protected function dtoFromArray(
        array $fields,
        bool $includeId = false
    ): string {

        $lines = [];

        foreach ($fields as $field) {

            if (
                !$includeId &&
                $field['name'] === 'id'
            ) {
                continue;
            }

            $name = $field['name'];
            $type = $field['type'];
            $nullable = $field['nullable'];

            $cast = match ($type) {
                'int' => '(int)',
                'float' => '(float)',
                'bool' => '(bool)',
                default => '(string)',
            };

            if ($nullable) {
                $value = sprintf(
                    '$data[\'%s\'] !== null ? %s$data[\'%s\'] : null',
                    $name,
                    $cast,
                    $name
                );
            } else {
                $value = sprintf(
                    '%s$data[\'%s\']',
                    $cast,
                    $name
                );
            }

            $lines[] = sprintf(
                '            %s: %s,',
                $name,
                $value
            );
        }

        return implode("\n", $lines);
    }

    /*
    |--------------------------------------------------------------------------
    | DTO ATTRIBUTES
    |--------------------------------------------------------------------------
    */

    protected function dtoAttributes(
        array $fields
    ): string {

        $lines = [];

        foreach ($fields as $field) {

            if ($field['name'] === 'id') {
                continue;
            }

            $name = $field['name'];

            $lines[] = sprintf(
                "            '%s' => \$this->%s,",
                $name,
                $name
            );
        }

        return implode("\n", $lines);
    }

    /*
    |--------------------------------------------------------------------------
    | DIRECTORIES
    |--------------------------------------------------------------------------
    */

    protected function makeDirs(
        string $basePath
    ): void {

        $directories = [

            'Actions',

            'DTOs',

            'Livewire',

            'Tables',

            'Services',

            'Policies',
        ];

        foreach ($directories as $directory) {

            File::ensureDirectoryExists(
                "{$basePath}/{$directory}"
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | LIVEWIRE
    |--------------------------------------------------------------------------
    */

    protected function generateLivewire(
        string $basePath,
        array $data
    ): void {

        $entity = $data['entity'];

        $this->createSafe(
            "{$basePath}/Livewire/{$entity}Page.php",
            $this->stub(
                'livewire-page',
                $data
            )
        );

        $this->createSafe(
            "{$basePath}/Livewire/{$entity}Form.php",
            $this->stub(
                'livewire-form',
                $data
            )
        );

        $this->createSafe(
            "{$basePath}/Livewire/{$entity}Table.php",
            $this->stub(
                'livewire-table',
                $data
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | OPTIONAL LIVEWIRE
    |--------------------------------------------------------------------------
    */

    protected function generateOptionalLivewire(
        string $basePath,
        array $data
    ): void {

        $entity = $data['entity'];

        if ($this->option('media')) {

            $this->createSafe(
                "{$basePath}/Livewire/{$entity}MediaManager.php",
                $this->stub(
                    'livewire-media',
                    $data
                )
            );
        }

        if ($this->option('attributes')) {

            $this->createSafe(
                "{$basePath}/Livewire/{$entity}AttributesManager.php",
                $this->stub(
                    'livewire-attributes',
                    $data
                )
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | TABLES
    |--------------------------------------------------------------------------
    */

    protected function generateTables(
        string $basePath,
        array $data
    ): void {

        $entity = $data['entity'];

        $this->createSafe(
            "{$basePath}/Tables/{$entity}TableDefinition.php",
            $this->stub(
                'table-definition',
                $data
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | CORE
    |--------------------------------------------------------------------------
    */

    protected function generateCore(
        string $basePath,
        array $data
    ): void {

        $entity = $data['entity'];

        $this->createSafe(
            "{$basePath}/Services/{$entity}Service.php",
            $this->stub(
                'service',
                $data
            )
        );

        $this->createSafe(
            "{$basePath}/Policies/{$entity}Policy.php",
            $this->stub(
                'policy',
                $data
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | DTOS
    |--------------------------------------------------------------------------
    */

    protected function generateDTOs(
        string $basePath,
        array $data
    ): void {

        $entity = $data['entity'];

        $this->createSafe(
            "{$basePath}/DTOs/Create{$entity}DTO.php",
            $this->stub(
                'dto-create',
                $data
            )
        );

        $this->createSafe(
            "{$basePath}/DTOs/Delete{$entity}DTO.php",
            $this->stub(
                'dto-delete',
                $data
            )
        );

        $this->createSafe(
            "{$basePath}/DTOs/Update{$entity}DTO.php",
            $this->stub(
                'dto-update',
                $data
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ACTIONS
    |--------------------------------------------------------------------------
    */

    protected function generateActions(
        string $basePath,
        array $data
    ): void {

        $entity = $data['entity'];

        $this->createSafe(
            "{$basePath}/Actions/Create{$entity}Action.php",
            $this->stub(
                'action-create',
                $data
            )
        );

        $this->createSafe(
            "{$basePath}/Actions/Update{$entity}Action.php",
            $this->stub(
                'action-update',
                $data
            )
        );

        $this->createSafe(
            "{$basePath}/Actions/Delete{$entity}Action.php",
            $this->stub(
                'action-delete',
                $data
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | VIEWS
    |--------------------------------------------------------------------------
    */

    protected function generateViews(
        array $data
    ): void {

        $moduleLower = $data['moduleLower'];

        $entityLower = $data['entityLower'];

        $path = resource_path(
            "views/modules/{$moduleLower}/{$entityLower}"
        );

        File::ensureDirectoryExists(
            $path
        );

        foreach ([
            'page',
            'form',
            'table',
        ] as $view) {

            $this->createSafe(
                "{$path}/{$view}.blade.php",
                $this->stub(
                    "view-{$view}",
                    $data
                )
            );
        }

        if ($this->option('media')) {

            $this->createSafe(
                "{$path}/media-manager.blade.php",
                $this->stub(
                    'view-media-manager',
                    $data
                )
            );
        }

        if ($this->option('attributes')) {

            $this->createSafe(
                "{$path}/attributes-manager.blade.php",
                $this->stub(
                    'view-attributes-manager',
                    $data
                )
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | SAFE WRITE
    |--------------------------------------------------------------------------
    */

    protected function createSafe(
        string $path,
        string $content
    ): void {

        $checksums = $this->loadChecksums();

        $relativePath = str_replace(
            base_path() . DIRECTORY_SEPARATOR,
            '',
            $path
        );

        $newHash = md5($content);

        if (!File::exists($path)) {

            File::put(
                $path,
                $content
            );

            $checksums[$relativePath] = $newHash;

            $this->saveChecksums(
                $checksums
            );

            $this->created++;

            $this->components->info(
                "CREATED {$relativePath}"
            );

            return;
        }

        $currentContent = File::get(
            $path
        );

        $currentHash = md5(
            $currentContent
        );

        $originalHash =
            $checksums[$relativePath] ?? null;

        if (
            $originalHash &&
            $currentHash === $originalHash
        ) {

            File::put(
                $path,
                $content
            );

            $checksums[$relativePath] = $newHash;

            $this->saveChecksums(
                $checksums
            );

            $this->created++;

            $this->components->info(
                "UPDATED {$relativePath}"
            );

            return;
        }

        if ($this->option('force')) {

            File::copy(
                $path,
                $path . '.bak'
            );

            File::put(
                $path,
                $content
            );

            $checksums[$relativePath] = $newHash;

            $this->saveChecksums(
                $checksums
            );

            $this->components->warn(
                "FORCED {$relativePath}"
            );

            return;
        }

        $this->components->warn(
            "MODIFIED {$relativePath}"
        );

        if ($this->confirm(
            'Overwrite file?',
            false
        )) {

            File::copy(
                $path,
                $path . '.bak'
            );

            File::put(
                $path,
                $content
            );

            $checksums[$relativePath] = $newHash;

            $this->saveChecksums(
                $checksums
            );

            $this->components->info(
                "OVERWRITTEN {$relativePath}"
            );

            return;
        }

        $this->skipped++;
    }

    /*
    |--------------------------------------------------------------------------
    | STUB LOADER
    |--------------------------------------------------------------------------
    */

    protected function stub(
        string $type,
        array $data
    ): string {

        $map = [

            /*
            |--------------------------------------------------------------------------
            | LIVEWIRE
            |--------------------------------------------------------------------------
            */

            'livewire-page'
                => 'livewire/page.stub',

            'livewire-form'
                => 'livewire/form.stub',

            'livewire-table'
                => 'livewire/table.stub',

            'livewire-media'
                => 'livewire/media-manager.stub',

            'livewire-attributes'
                => 'livewire/attributes-manager.stub',

            /*
            |--------------------------------------------------------------------------
            | ACTIONS
            |--------------------------------------------------------------------------
            */

            'action-create'
                => 'actions/action-create.stub',

            'action-update'
                => 'actions/action-update.stub',

            'action-delete'
                => 'actions/action-delete.stub',

            /*
            |--------------------------------------------------------------------------
            | TABLES
            |--------------------------------------------------------------------------
            */

            'table-definition'
                => 'tables/table-definition.stub',

            /*
            |--------------------------------------------------------------------------
            | CORE
            |--------------------------------------------------------------------------
            */

            'service'
                => 'core/service.stub',

            'policy'
                => 'core/policy.stub',

            /*
            |--------------------------------------------------------------------------
            | DTOS
            |--------------------------------------------------------------------------
            */

            'dto-create'
                => 'dto/dto-create.stub',

            'dto-delete'
                => 'dto/dto-delete.stub',

            'dto-update'
                => 'dto/dto-update.stub',

            /*
            |--------------------------------------------------------------------------
            | VIEWS
            |--------------------------------------------------------------------------
            */

            'view-page'
                => 'views/page.stub',

            'view-form'
                => 'views/form.stub',

            'view-table'
                => 'views/table.stub',

            'view-media-manager'
                => 'views/media-manager.stub',

            'view-attributes-manager'
                => 'views/attributes-manager.stub',
        ];

        if (!isset($map[$type])) {
            throw new \Exception(
                "Stub type no definido: {$type}"
            );
        }

        $path = base_path(
            'stubs/modules/' . $map[$type]
        );

        if (!File::exists($path)) {
            throw new \Exception(
                "Stub no encontrado: {$path}"
            );
        }

        $stub = File::get($path);

        foreach ($data as $key => $value) {

            // Este dato puede ser un array y no necesariamente
            // es utilizado por este stub.
            if (!is_scalar($value) && $value !== null) {
                continue;
            }

            $stub = str_replace(
                "{{{$key}}}",
                (string) $value,
                $stub
            );
        }

        return $stub;
    }

    /*
    |--------------------------------------------------------------------------
    | CHECKSUMS
    |--------------------------------------------------------------------------
    */

    protected function loadChecksums(): array
    {
        if (!File::exists(
            $this->checksumFile
        )) {

            return [];
        }

        $data = json_decode(
            File::get(
                $this->checksumFile
            ),
            true
        );

        return is_array($data)
            ? $data
            : [];
    }

    protected function saveChecksums(
        array $data
    ): void {

        File::put(
            $this->checksumFile,
            json_encode(
                $data,
                JSON_PRETTY_PRINT |
                JSON_UNESCAPED_SLASHES
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | SUMMARY
    |--------------------------------------------------------------------------
    */

    protected function printSummary(
        string $module,
        string $entity
    ): void {

        $this->line(
            '────────────────────────'
        );

        $this->components->info(
            "Module {$module}/{$entity} generated"
        );

        $this->line(
            "Created: {$this->created}"
        );

        $this->line(
            "Skipped: {$this->skipped}"
        );

        $this->line(
            '────────────────────────'
        );
    }
}