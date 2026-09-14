<?php

namespace App\Modules\Comercial\Gestor\DTOs;

class CreateGestorDTO
{
    public function __construct(
        public readonly int $user_id,
        public readonly string $nombre_comercial,
        public readonly ?string $descripcion,
        public readonly bool $activo,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            user_id: (int) $data['user_id'],
            nombre_comercial: (string) $data['nombre_comercial'],
            descripcion: $data['descripcion'] !== null
                ? (string) $data['descripcion']
                : null,
            activo: (bool) $data['activo'],
        );
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->user_id,
            'nombre_comercial' => $this->nombre_comercial,
            'descripcion' => $this->descripcion,
            'activo' => $this->activo,
        ];
    }
}