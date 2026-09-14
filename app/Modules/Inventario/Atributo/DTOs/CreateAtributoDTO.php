<?php

namespace App\Modules\Inventario\Atributo\DTOs;

class CreateAtributoDTO
{
    public function __construct(
        public readonly string $nombre,
        public readonly ?string $descripcion,
        public readonly ?int $orden_visual,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            nombre: (string)$data['nombre'],
            descripcion: $data['descripcion'] !== null ? (string)$data['descripcion'] : null,
            orden_visual: $data['orden_visual'] !== null ? (int)$data['orden_visual'] : null,
        );
    }

    public function toArray(): array
    {
        return [
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'orden_visual' => $this->orden_visual,
        ];
    }
}