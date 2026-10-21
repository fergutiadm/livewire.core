<?php

namespace App\Modules\Admin\Denominacion\DTOs;

class DeleteDenominacionDTO
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