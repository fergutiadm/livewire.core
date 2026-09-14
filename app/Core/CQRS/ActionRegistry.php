<?php

namespace App\Core\CQRS;

class ActionRegistry
{
    protected array $actions = [];

    public function register(
        string $command,
        string $action
    ): void {
        $this->actions[$command] = $action;
    }

    public function resolve(
        string $command
    ): ?string {

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

        [$entity, $action] = explode('.', $command);

        $entity = ucfirst($entity);
        $action = ucfirst($action);

        /*
        |--------------------------------------------------------------------------
        | Convención CQRS
        |--------------------------------------------------------------------------
        |
        | producto.create
        | ↓
        | CreateProductoAction
        |
        */

        $class = "App\\Modules\\Inventario\\{$entity}\\Actions\\{$action}{$entity}Action";

        return class_exists($class)
            ? $class
            : null;
    }
}