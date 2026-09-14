<?php

namespace App\Modules\Inventario\ProductoUnidad\DTOs;

class DeleteProductoUnidadDTO
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