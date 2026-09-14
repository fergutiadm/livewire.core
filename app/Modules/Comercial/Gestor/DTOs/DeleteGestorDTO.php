<?php

namespace App\Modules\Comercial\Gestor\DTOs;

class DeleteGestorDTO
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