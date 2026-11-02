<?php

namespace App\Modules\Ayuda\AyudaArticulo\DTOs;

class DeleteAyudaArticuloDTO
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