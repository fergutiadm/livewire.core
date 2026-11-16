<?php

namespace App\Modules\Inventario\Local\DTOs;

class CreateLocalDTO
{
    public function __construct(
        public readonly int $tipo_local_id,
        public readonly string $nombre,
        public readonly ?string $descripcion,
        public readonly bool $es_principal,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            tipo_local_id: (int) $data['tipo_local_id'],
            nombre: (string)$data['nombre'],
            descripcion: $data['descripcion'] !== null ? (string)$data['descripcion'] : null,
            es_principal: (bool)$data['es_principal'],
        );
    }

    public function toArray(): array
    {
        return [
            'tipo_local_id' => $this->tipo_local_id,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'es_principal' => $this->es_principal,
        ];
    }
}