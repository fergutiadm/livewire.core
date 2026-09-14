<?php

namespace App\Modules\Inventario\Local\DTOs;

class CreateLocalDTO
{
    public function __construct(
        public readonly string $nombre,
        public readonly ?string $descripcion,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            nombre: (string)$data['nombre'],
            descripcion: $data['descripcion'] !== null ? (string)$data['descripcion'] : null,
        );
    }

    public function toArray(): array
    {
        return [
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
        ];
    }
}