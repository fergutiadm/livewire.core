<?php

namespace App\Core\CQRS;

use Exception;

class CommandBus
{
    public function __construct(
        protected ActionRegistry $registry
    ) {}

    public function dispatch(
        string $command,
        array $payload = []
    ) {

        $actionClass = $this->registry
            ->resolve($command);

        if (!$actionClass) {

            throw new Exception(
                "Action not found for command [{$command}]"
            );
        }

        $dto = $this->resolveDTO(
            $actionClass,
            $payload
        );

        return app($actionClass)($dto);
    }

    protected function resolveDTO(
        string $actionClass,
        array $payload
    ) {

        /*
        |--------------------------------------------------------------------------
        | Action → DTO Resolver
        |--------------------------------------------------------------------------
        |
        | CreateProductoAction
        | ↓
        | CreateProductoDTO
        |
        */

        $dtoClass = str_replace(
            '\\Actions\\',
            '\\DTOs\\',
            $actionClass
        );

        $dtoClass = str_replace(
            'Action',
            'DTO',
            $dtoClass
        );

        if (!class_exists($dtoClass)) {
            return null;
        }

        return $dtoClass::fromArray(
            $payload
        );
    }
}