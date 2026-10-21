<?php

namespace App\Modules\Finanzas\CierreCaja\DTOs;

final class UpdateCierreCajaDTO
{
    public function __construct(
        public readonly int $id,
        public readonly int $local_id,
        public readonly int $caja_id,
        public readonly int $periodo_contable_id,
        public readonly int $operador_id,
        public readonly string $fecha,
        public readonly array $cierreMonedas,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) $data['id'],
            local_id: (int) $data['local_id'],
            caja_id: (int) $data['caja_id'],
            periodo_contable_id: (int) $data['periodo_contable_id'],
            operador_id: (int) $data['operador_id'],
            fecha: (string) $data['fecha'],
            cierreMonedas: $data['cierreMonedas'] ?? [],
        );
    }

    public function attributes(): array
    {
        return [
            'local_id' => $this->local_id,
            'caja_id' => $this->caja_id,
            'periodo_contable_id' => $this->periodo_contable_id,
            'operador_id' => $this->operador_id,
            'fecha' => $this->fecha,
        ];
    }
}