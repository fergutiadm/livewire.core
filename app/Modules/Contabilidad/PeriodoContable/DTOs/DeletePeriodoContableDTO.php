<?php

namespace App\Modules\Contabilidad\PeriodoContable\DTOs;

class DeletePeriodoContableDTO
{
    public function __construct(
        public readonly int $id
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) $data['id'],
        );
    }
}