<?php

namespace App\Modules\Inventario\Atributo\DTOs;

class DeleteAtributoDTO
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