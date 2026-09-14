<?php

namespace App\Modules\Contabilidad\PeriodoContable\DTOs;

class UpdatePeriodoContableDTO
{
    public function __construct(
        public readonly int $id,
        public readonly int $local_id,
        public readonly ?string $nombre,
        public readonly string $fecha_inicio,
        public readonly string $fecha_fin,
        public readonly bool $activo,
        public readonly bool $cerrado,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) $data['id'],
            local_id: (int)$data['local_id'],
            nombre: $data['nombre'] !== null ? (string)$data['nombre'] : null,
            fecha_inicio: (string)$data['fecha_inicio'],
            fecha_fin: (string)$data['fecha_fin'],
            activo: (bool)$data['activo'],
            cerrado: (bool)$data['cerrado'],
        );
    }

    public function attributes(): array
    {
        return [
            'local_id' => $this->local_id,
            'nombre' => $this->nombre,
            'fecha_inicio' => $this->fecha_inicio,
            'fecha_fin' => $this->fecha_fin,
            'activo' => $this->activo,
            'cerrado' => $this->cerrado,
        ];
    }
}