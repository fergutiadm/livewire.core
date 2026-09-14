<?php

namespace App\Modules\Inventario\UnidadDimension\DTOs;

class UpdateUnidadDimensionDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $codigo,
        public readonly string $nombre,
        public readonly ?string $descripcion,
        public readonly bool $activo,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) $data['id'],
            codigo: (string)$data['codigo'],
            nombre: (string)$data['nombre'],
            descripcion: $data['descripcion'] !== null ? (string)$data['descripcion'] : null,
            activo: (bool)$data['activo'],
        );
    }

    public function attributes(): array
    {
        return [
            'codigo' => $this->codigo,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'activo' => $this->activo,
        ];
    }
}