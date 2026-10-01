<?php

namespace App\Modules\Gastos\GastoPago\DTOs;

class UpdateGastoPagoDTO
{
    public function __construct(
        public readonly int $id,

    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) $data['id'],

        );
    }

    public function attributes(): array
    {
        return [

        ];
    }
}