<?php

namespace App\Modules\Gastos\TipoGasto\DTOs;

class UpdateTipoGastoDTO
{
    public function __construct(
        public readonly int $id,
        public readonly int $local_id,
        public readonly string $nombre,
        public readonly ?string $descripcion,
        public readonly int $fijo,
        public readonly int $activo,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) $data['id'],
            local_id: (int)$data['local_id'],
            nombre: (string)$data['nombre'],
            descripcion: $data['descripcion'] !== null ? (string)$data['descripcion'] : null,
            fijo: (int)$data['fijo'],
            activo: (int)$data['activo'],
        );
    }

    public function attributes(): array
    {
        return [
            'local_id' => $this->local_id,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'fijo' => $this->fijo,
            'activo' => $this->activo,
        ];
    }
}