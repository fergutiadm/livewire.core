<?php

namespace App\Modules\Inventario\Local\DTOs;

class UpdateLocalDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $nombre,
        public readonly ?string $descripcion,
        public readonly bool $es_principal,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) $data['id'],
            nombre: (string)$data['nombre'],
            descripcion: $data['descripcion'] !== null ? (string)$data['descripcion'] : null,
            es_principal: (bool)$data['es_principal'],
        );
    }

    public function attributes(): array
    {
        return [
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'es_principal' => $this->es_principal,
        ];
    }
}