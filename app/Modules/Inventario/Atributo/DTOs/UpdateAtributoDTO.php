<?php

namespace App\Modules\Inventario\Atributo\DTOs;

class UpdateAtributoDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $nombre,
        public readonly ?string $descripcion,
        public readonly ?int $orden_visual,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) $data['id'],
            nombre: (string)$data['nombre'],
            descripcion: $data['descripcion'] !== null ? (string)$data['descripcion'] : null,
            orden_visual: $data['orden_visual'] !== null ? (int)$data['orden_visual'] : null,
        );
    }

    public function attributes(): array
    {
        return [
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'orden_visual' => $this->orden_visual,
        ];
    }
}