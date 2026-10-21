<?php

namespace App\Modules\Gastos\Gasto\DTOs;

class CreateGastoDTO
{
    public function __construct(
        public readonly int $local_id,
        public readonly int $tipo_gasto_id,
        public readonly int $periodo_contable_id,
        public readonly ?int $especialista_id,
        public readonly ?int $responsable_id,
        public readonly float $cantidad,
        public readonly float $valor,
        public readonly int $moneda_id,
        public readonly float $tasa_cambio,
        public readonly ?float $tasa_cambio_usd,
        public readonly ?string $descripcion,
        public readonly string $fecha,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            local_id: (int) $data['local_id'],
            tipo_gasto_id: (int) $data['tipo_gasto_id'],
            periodo_contable_id: (int) $data['periodo_contable_id'],
            especialista_id: $data['especialista_id'] !== null
                ? (int) $data['especialista_id']
                : null,
            responsable_id: $data['responsable_id'] !== null
                ? (int) $data['responsable_id']
                : null,
            cantidad: (float) $data['cantidad'],
            valor: (float) $data['valor'],
            moneda_id: (int) $data['moneda_id'],
            tasa_cambio: (float) $data['tasa_cambio'],
            tasa_cambio_usd: $data['tasa_cambio_usd'] !== null
                ? (float) $data['tasa_cambio_usd']
                : null,
            descripcion: $data['descripcion'] !== null
                ? (string) $data['descripcion']
                : null,
            fecha: (string) $data['fecha'],
        );
    }

    public function toArray(): array
    {
        return [
            'local_id' => $this->local_id,
            'tipo_gasto_id' => $this->tipo_gasto_id,
            'periodo_contable_id' => $this->periodo_contable_id,
            'especialista_id' => $this->especialista_id,
            'responsable_id' => $this->responsable_id,
            'cantidad' => $this->cantidad,
            'valor' => $this->valor,
            'moneda_id' => $this->moneda_id,
            'tasa_cambio' => $this->tasa_cambio,
            'tasa_cambio_usd' => $this->tasa_cambio_usd,
            'descripcion' => $this->descripcion,
            'fecha' => $this->fecha,
        ];
    }
}