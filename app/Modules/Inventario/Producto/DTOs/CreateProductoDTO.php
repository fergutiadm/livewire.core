<?php

namespace App\Modules\Inventario\Producto\DTOs;

class CreateProductoDTO
{
    public function __construct(
        public readonly string $nombre,
        public readonly int $local_id,
        public readonly int $categoria_id,
        public readonly int $moneda_id,
        public readonly float $costo,
        public readonly float $precio,
        public readonly float $porciento_descuento,
        public readonly string $codigo,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            nombre: (string) $data['nombre'],
            local_id: (int) $data['local_id'],
            categoria_id: (int) $data['categoria_id'],
            moneda_id: (int) $data['moneda_id'],
            costo: (float) $data['costo'],
            precio: (float) $data['precio'],
            porciento_descuento: (float) ($data['porciento_descuento'] ?? 0),
            codigo: (string) $data['codigo'],
        );
    }

    public function toArray(): array
    {
        return [
            'nombre' => $this->nombre,
            'local_id' => $this->local_id,
            'categoria_id' => $this->categoria_id,
            'moneda_id' => $this->moneda_id,
            'costo' => $this->costo,
            'precio' => $this->precio,
            'porciento_descuento' => $this->porciento_descuento,
            'codigo' => $this->codigo,
        ];
    }
}