<?php

namespace App\Core\CQRS;

use RuntimeException;

class ActionRegistry
{
    protected array $actions = [];

    /**
     * Índice interno:
     *
     * [
     *     'producto'  => 'Inventario',
     *     'gasto'     => 'Gastos',
     *     'tipoGasto' => 'Gastos',
     * ]
     *
     * Se construye automáticamente a partir de:
     *
     * App\Modules\{Modulo}\{Entidad}
     */
    protected ?array $entityModules = null;

    public function register(
        string $command,
        string $action
    ): void {
        $this->actions[$command] = $action;
    }

    public function resolve(
        string $command
    ): ?string {

        /*
        |--------------------------------------------------------------------------
        | Registro explícito
        |--------------------------------------------------------------------------
        |
        | Permite sobrescribir la resolución automática cuando sea necesario.
        |
        */
        if (isset($this->actions[$command])) {
            return $this->actions[$command];
        }

        return $this->autoResolve($command);
    }

    protected function autoResolve(
        string $command
    ): ?string {

        if (!str_contains($command, '.')) {
            return null;
        }

        [$entity, $action] = explode('.', $command, 2);

        if ($entity === '' || $action === '') {
            return null;
        }

        /*
        |--------------------------------------------------------------------------
        | Descubrir módulo de la entidad
        |--------------------------------------------------------------------------
        |
        | producto.create
        |     ↓
        | App\Modules\Inventario\Producto
        |
        | gasto.create
        |     ↓
        | App\Modules\Gastos\Gasto
        |
        */
        $module = $this->findModuleForEntity($entity);

        if ($module === null) {
            return null;
        }

        $entityClass = ucfirst($entity);
        $actionClass = ucfirst($action);

        /*
        |--------------------------------------------------------------------------
        | Convención CQRS
        |--------------------------------------------------------------------------
        |
        | producto.create
        |     ↓
        | CreateProductoAction
        |
        | gasto.create
        |     ↓
        | CreateGastoAction
        |
        | tipoGasto.create
        |     ↓
        | CreateTipoGastoAction
        |
        */
        $class = "App\\Modules\\{$module}\\{$entityClass}\\Actions\\{$actionClass}{$entityClass}Action";

        return class_exists($class)
            ? $class
            : null;
    }

    /**
     * Busca automáticamente el módulo al que pertenece una entidad.
     *
     * No hay módulos hardcodeados aquí.
     *
     * App\Modules\
     *     Inventario\
     *         Producto\
     *     Gastos\
     *         Gasto\
     *         TipoGasto\
     *     Finanzas\
     *         Moneda\
     */
    protected function findModuleForEntity(
        string $entity
    ): ?string {

        /*
        |--------------------------------------------------------------------------
        | Construir índice una sola vez por instancia
        |--------------------------------------------------------------------------
        */
        if ($this->entityModules !== null) {
            return $this->entityModules[$entity] ?? null;
        }

        $this->entityModules = [];

        $modulesPath = app_path('Modules');

        if (!is_dir($modulesPath)) {
            return null;
        }

        $modules = new \DirectoryIterator($modulesPath);

        foreach ($modules as $moduleDirectory) {

            if (
                $moduleDirectory->isDot()
                || !$moduleDirectory->isDir()
            ) {
                continue;
            }

            $module = $moduleDirectory->getFilename();

            $entitiesPath = $moduleDirectory->getPathname();

            $entities = new \DirectoryIterator($entitiesPath);

            foreach ($entities as $entityDirectory) {

                if (
                    $entityDirectory->isDot()
                    || !$entityDirectory->isDir()
                ) {
                    continue;
                }

                $entityClass = $entityDirectory->getFilename();

                /*
                |--------------------------------------------------------------------------
                | El comando utiliza lowerCamelCase
                |
                | TipoGasto → tipoGasto
                | GastoPago → gastoPago
                | Producto  → producto
                |--------------------------------------------------------------------------
                */
                $commandEntity = lcfirst($entityClass);

                /*
                |--------------------------------------------------------------------------
                | Detectar entidades duplicadas entre módulos
                |--------------------------------------------------------------------------
                |
                | Si algún día tenemos:
                |
                | Comercial\Proveedor
                | Compras\Proveedor
                |
                | proveedor.create sería ambiguo.
                |
                | No elegimos arbitrariamente uno.
                |--------------------------------------------------------------------------
                */
                if (isset($this->entityModules[$commandEntity])) {

                    $existingModule =
                        $this->entityModules[$commandEntity];

                    throw new RuntimeException(
                        sprintf(
                            'Ambiguous CQRS entity [%s]. ' .
                            'Found in modules [%s] and [%s].',
                            $commandEntity,
                            $existingModule,
                            $module
                        )
                    );
                }

                $this->entityModules[$commandEntity] = $module;
            }
        }

        return $this->entityModules[$entity] ?? null;
    }
}