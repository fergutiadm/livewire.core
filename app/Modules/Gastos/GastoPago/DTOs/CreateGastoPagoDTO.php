<?php

namespace App\Modules\Gastos\GastoPago\DTOs;

class CreateGastoPagoDTO
{
    public function __construct(

    ) {}

    public static function fromArray(array $data): self
    {
        return new self(

        );
    }

    public function toArray(): array
    {
        return [

        ];
    }
}