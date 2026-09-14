<?php

namespace App\Modules\Inventario\Producto\DTOs;

class SaveProductoAttributesDTO
{
    public function __construct(
        public readonly int $productoId,
        public readonly array $cards,
    ) {
    }
}
