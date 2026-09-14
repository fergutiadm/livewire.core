<?php

namespace App\Modules\Inventario\ProductoUnidad\DTOs;

class CreateProductoUnidadDTO
{
    public function __construct(
        public readonly int $producto_id,
        public readonly int $unidad_medida_id,
        public readonly float $factor_a_base,
        public readonly bool $activo,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            producto_id: (int)$data['producto_id'],
            unidad_medida_id: (int)$data['unidad_medida_id'],
            factor_a_base: (float)$data['factor_a_base'],
            activo: (bool)$data['activo'],
        );
    }

    public function toArray(): array
    {
        return [
            'producto_id' => $this->producto_id,
            'unidad_medida_id' => $this->unidad_medida_id,
            'factor_a_base' => $this->factor_a_base,
            'activo' => $this->activo,
        ];
    }
}