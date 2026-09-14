<?php

namespace App\Modules\Comercial\Gestor\DTOs;

class UpdateGestorDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $nombre_comercial,
        public readonly ?string $descripcion,
        public readonly bool $activo,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) $data['id'],
            nombre_comercial: (string) $data['nombre_comercial'],
            descripcion: $data['descripcion'] !== null
                ? (string) $data['descripcion']
                : null,
            activo: (bool) $data['activo'],
        );
    }

    public function attributes(): array
    {
        return [
            'nombre_comercial' => $this->nombre_comercial,
            'descripcion' => $this->descripcion,
            'activo' => $this->activo,
        ];
    }
}