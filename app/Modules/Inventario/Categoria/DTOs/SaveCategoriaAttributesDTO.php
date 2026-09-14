<?php

namespace App\Modules\Inventario\Categoria\DTOs;

class SaveCategoriaAttributesDTO
{
    public function __construct(
        public readonly int $categoriaId,
        public readonly array $cards,
    ) {
    }
}