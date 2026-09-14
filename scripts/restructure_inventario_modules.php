<?php

/**
 * Reestructura los módulos Inventario:
 *
 * ANTES:
 *
 * app/Modules/Inventario/
 * ├── Actions/
 * ├── DTOs/
 * ├── Livewire/
 * │   ├── Categoria/
 * │   ├── Local/
 * │   └── Producto/
 * ├── Tables/
 * ├── Services/
 * └── Policies/
 *
 * DESPUÉS:
 *
 * app/Modules/Inventario/
 * ├── Categoria/
 * │   ├── Actions/
 * │   ├── DTOs/
 * │   ├── Livewire/
 * │   ├── Tables/
 * │   ├── Services/
 * │   └── Policies/
 * ├── Local/
 * └── Producto/
 *
 * Uso:
 *
 *   php scripts/restructure_inventario_modules.php
 *
 * Simulación.
 *
 *   php scripts/restructure_inventario_modules.php --apply
 *
 * Ejecuta los cambios.
 */

declare(strict_types=1);

$apply = in_array('--apply', $argv, true);

$basePath = dirname(__DIR__);

$modulesPath = $basePath . DIRECTORY_SEPARATOR
    . 'app'
    . DIRECTORY_SEPARATOR
    . 'Modules'
    . DIRECTORY_SEPARATOR
    . 'Inventario';

$entities = [
    'Categoria',
    'Local',
    'Producto',
];

$directories = [
    'Actions',
    'DTOs',
    'Livewire',
    'Tables',
    'Services',
    'Policies',
];

/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/

function line(string $message = ''): void
{
    echo $message . PHP_EOL;
}

function section(string $title): void
{
    line();
    line('============================================================');
    line($title);
    line('============================================================');
}

function ensureDirectory(string $path, bool $apply): void
{
    if (is_dir($path)) {
        return;
    }

    line("  CREATE DIR: {$path}");

    if ($apply) {
        mkdir($path, 0777, true);
    }
}

function moveFile(
    string $source,
    string $destination,
    bool $apply
): bool {

    if (!file_exists($source)) {
        return false;
    }

    if (file_exists($destination)) {

        line("  SKIP: destination already exists");

        line("        SOURCE: {$source}");
        line("        TARGET: {$destination}");

        return false;
    }

    line("  MOVE:");
    line("       {$source}");
    line("       -> {$destination}");

    if ($apply) {

        ensureDirectory(
            dirname($destination),
            true
        );

        if (!rename($source, $destination)) {

            throw new RuntimeException(
                "No se pudo mover:\n{$source}\n->\n{$destination}"
            );
        }
    }

    return true;
}

function updateNamespace(
    string $file,
    string $oldNamespace,
    string $newNamespace,
    bool $apply
): void {

    if (!file_exists($file)) {
        return;
    }

    $content = file_get_contents($file);

    if ($content === false) {
        throw new RuntimeException(
            "No se pudo leer: {$file}"
        );
    }

    $old = "namespace {$oldNamespace};";
    $new = "namespace {$newNamespace};";

    if (!str_contains($content, $old)) {

        /*
         * No abortamos.
         *
         * El archivo puede haber sido modificado previamente
         * o puede utilizar otro formato de namespace.
         */
        line("  NAMESPACE NOT FOUND:");
        line("       {$old}");
        line("       {$file}");

        return;
    }

    line("  NAMESPACE:");
    line("       {$oldNamespace}");
    line("       -> {$newNamespace}");

    if ($apply) {

        $content = str_replace(
            $old,
            $new,
            $content
        );

        file_put_contents(
            $file,
            $content
        );
    }
}

function processFile(
    string $source,
    string $destination,
    string $oldNamespace,
    string $newNamespace,
    bool $apply
): void {

    if (!file_exists($source)) {
        return;
    }

    if (file_exists($destination)) {

        line("  SKIP EXISTING:");
        line("       {$destination}");

        return;
    }

    /*
     * Primero movemos.
     */
    moveFile(
        $source,
        $destination,
        $apply
    );

    /*
     * Si estamos en modo simulación no existe físicamente
     * el destino todavía, por lo que solamente mostramos
     * el cambio.
     */
    if ($apply) {

        updateNamespace(
            $destination,
            $oldNamespace,
            $newNamespace,
            true
        );

    } else {

        line("  NAMESPACE:");
        line("       {$oldNamespace}");
        line("       -> {$newNamespace}");
    }
}

/*
|--------------------------------------------------------------------------
| Inicio
|--------------------------------------------------------------------------
*/

section(
    'REESTRUCTURACIÓN DE MÓDULOS INVENTARIO'
);

line(
    $apply
        ? 'MODO: APPLY — se realizarán cambios'
        : 'MODO: DRY-RUN — no se modificará ningún archivo'
);

line();

if (!is_dir($modulesPath)) {

    throw new RuntimeException(
        "No existe el directorio:\n{$modulesPath}"
    );
}

/*
|--------------------------------------------------------------------------
| Procesamiento
|--------------------------------------------------------------------------
*/

foreach ($entities as $entity) {

    section("ENTIDAD: {$entity}");

    $entityRoot =
        $modulesPath
        . DIRECTORY_SEPARATOR
        . $entity;

    /*
     * Crear estructura.
     */
    foreach ($directories as $directory) {

        ensureDirectory(
            $entityRoot
            . DIRECTORY_SEPARATOR
            . $directory,
            $apply
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ACTIONS
    |--------------------------------------------------------------------------
    */

    $sharedActions =
        $modulesPath
        . DIRECTORY_SEPARATOR
        . 'Actions';

    $entityActions =
        $entityRoot
        . DIRECTORY_SEPARATOR
        . 'Actions';

    $actions = [
        "Create{$entity}Action.php",
        "Update{$entity}Action.php",
        "Delete{$entity}Action.php",
    ];

    foreach ($actions as $file) {

        processFile(
            $sharedActions . DIRECTORY_SEPARATOR . $file,
            $entityActions . DIRECTORY_SEPARATOR . $file,
            'App\\Modules\\Inventario\\Actions',
            "App\\Modules\\Inventario\\{$entity}\\Actions",
            $apply
        );
    }

    /*
    |--------------------------------------------------------------------------
    | DTOS
    |--------------------------------------------------------------------------
    */

    $sharedDTOs =
        $modulesPath
        . DIRECTORY_SEPARATOR
        . 'DTOs';

    $entityDTOs =
        $entityRoot
        . DIRECTORY_SEPARATOR
        . 'DTOs';

    $dtos = [
        "Create{$entity}DTO.php",
        "Update{$entity}DTO.php",
    ];

    foreach ($dtos as $file) {

        processFile(
            $sharedDTOs . DIRECTORY_SEPARATOR . $file,
            $entityDTOs . DIRECTORY_SEPARATOR . $file,
            'App\\Modules\\Inventario\\DTOs',
            "App\\Modules\\Inventario\\{$entity}\\DTOs",
            $apply
        );
    }

    /*
    |--------------------------------------------------------------------------
    | TABLES
    |--------------------------------------------------------------------------
    */

    $sharedTables =
        $modulesPath
        . DIRECTORY_SEPARATOR
        . 'Tables';

    $entityTables =
        $entityRoot
        . DIRECTORY_SEPARATOR
        . 'Tables';

    $tableFile =
        "{$entity}TableDefinition.php";

    processFile(
        $sharedTables . DIRECTORY_SEPARATOR . $tableFile,
        $entityTables . DIRECTORY_SEPARATOR . $tableFile,
        'App\\Modules\\Inventario\\Tables',
        "App\\Modules\\Inventario\\{$entity}\\Tables",
        $apply
    );

    /*
    |--------------------------------------------------------------------------
    | SERVICES
    |--------------------------------------------------------------------------
    */

    $sharedServices =
        $modulesPath
        . DIRECTORY_SEPARATOR
        . 'Services';

    $entityServices =
        $entityRoot
        . DIRECTORY_SEPARATOR
        . 'Services';

    $serviceFile =
        "{$entity}Service.php";

    processFile(
        $sharedServices . DIRECTORY_SEPARATOR . $serviceFile,
        $entityServices . DIRECTORY_SEPARATOR . $serviceFile,
        'App\\Modules\\Inventario\\Services',
        "App\\Modules\\Inventario\\{$entity}\\Services",
        $apply
    );

    /*
    |--------------------------------------------------------------------------
    | POLICIES
    |--------------------------------------------------------------------------
    */

    $sharedPolicies =
        $modulesPath
        . DIRECTORY_SEPARATOR
        . 'Policies';

    $entityPolicies =
        $entityRoot
        . DIRECTORY_SEPARATOR
        . 'Policies';

    $policyFile =
        "{$entity}Policy.php";

    processFile(
        $sharedPolicies . DIRECTORY_SEPARATOR . $policyFile,
        $entityPolicies . DIRECTORY_SEPARATOR . $policyFile,
        'App\\Modules\\Inventario\\Policies',
        "App\\Modules\\Inventario\\{$entity}\\Policies",
        $apply
    );

    /*
    |--------------------------------------------------------------------------
    | LIVEWIRE
    |--------------------------------------------------------------------------
    |
    | Actualmente:
    |
    | Livewire/Categoria/CategoriaPage.php
    |
    | Queremos:
    |
    | Categoria/Livewire/CategoriaPage.php
    |
    */

    $sharedLivewire =
        $modulesPath
        . DIRECTORY_SEPARATOR
        . 'Livewire';

    $entityLivewire =
        $entityRoot
        . DIRECTORY_SEPARATOR
        . 'Livewire';

    $oldLivewirePath =
        $sharedLivewire
        . DIRECTORY_SEPARATOR
        . $entity;

    /*
     * Si existe el directorio antiguo Livewire/Categoria,
     * movemos todos los PHP que contiene.
     */
    if (is_dir($oldLivewirePath)) {

        $files = scandir($oldLivewirePath);

        if ($files !== false) {

            foreach ($files as $file) {

                if (
                    $file === '.'
                    || $file === '..'
                ) {
                    continue;
                }

                $source =
                    $oldLivewirePath
                    . DIRECTORY_SEPARATOR
                    . $file;

                if (!is_file($source)) {
                    continue;
                }

                $destination =
                    $entityLivewire
                    . DIRECTORY_SEPARATOR
                    . $file;

                /*
                 * Namespace antiguo:
                 *
                 * App\Modules\Inventario\Livewire\Categoria
                 *
                 * Nuevo:
                 *
                 * App\Modules\Inventario\Categoria\Livewire
                 */
                processFile(
                    $source,
                    $destination,
                    "App\\Modules\\Inventario\\Livewire\\{$entity}",
                    "App\\Modules\\Inventario\\{$entity}\\Livewire",
                    $apply
                );
            }
        }
    }

    /*
     * También soportamos Livewire directamente:
     *
     * Livewire/CategoriaPage.php
     *
     * por si alguno ya fue normalizado manualmente.
     */
    $directLivewireFiles = [
        "{$entity}Page.php",
        "{$entity}Form.php",
        "{$entity}Table.php",
        "{$entity}MediaManager.php",
        "{$entity}AttributesManager.php",
        "{$entity}AtributosManager.php",
    ];

    foreach ($directLivewireFiles as $file) {

        $source =
            $sharedLivewire
            . DIRECTORY_SEPARATOR
            . $file;

        $destination =
            $entityLivewire
            . DIRECTORY_SEPARATOR
            . $file;

        processFile(
            $source,
            $destination,
            'App\\Modules\\Inventario\\Livewire',
            "App\\Modules\\Inventario\\{$entity}\\Livewire",
            $apply
        );
    }
}

/*
|--------------------------------------------------------------------------
| Limpieza informativa
|--------------------------------------------------------------------------
*/

section('RESUMEN');

if (!$apply) {

    line(
        'Esto fue solamente una simulación.'
    );

    line();

    line(
        'Si el resultado es correcto ejecuta:'
    );

    line();

    line(
        'php scripts/restructure_inventario_modules.php --apply'
    );

} else {

    line(
        'Reestructuración terminada.'
    );

    line();

    line(
        'IMPORTANTE: ejecuta ahora:'
    );

    line();

    line(
        'composer dump-autoload'
    );
}   