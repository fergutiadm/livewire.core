<?php

namespace App\Modules\Finanzas\Caja\DTOs;

class CreateCajaDTO
{
    public function __construct(
        public readonly int $local_id,
        public readonly string $codigo,
        public readonly string $nombre,
        public readonly int $activa,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            local_id: (int)$data['local_id'],
            codigo: (string)$data['codigo'],
            nombre: (string)$data['nombre'],
            activa: (int)$data['activa'],
        );
    }

    public function toArray(): array
    {
        return [
            'local_id' => $this->local_id,
            'codigo' => $this->codigo,
            'nombre' => $this->nombre,
            'activa' => $this->activa,
        ];
    }
}