<?php

namespace App\Modules\Inventario\Atributo\DTOs;

class DeleteAtributoDTO
{
    public function __construct(
        public int $id,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) $data['id'],
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
        ];
    }
}