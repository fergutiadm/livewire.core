<?php

namespace App\Modules\Gastos\Gasto\DTOs;

class UpdateGastoDTO
{
    public function __construct(
        public readonly int $id,
        public readonly int $local_id,
        public readonly int $tipo_gasto_id,
        public readonly int $periodo_contable_id,
        public readonly ?int $especialista_id,
        public readonly ?int $responsable_id,
        public readonly int $operador_id,
        public readonly float $cantidad,
        public readonly float $valor,
        public readonly int $moneda_id,
        public readonly float $tasa_cambio,
        public readonly ?float $tasa_cambio_usd,
        public readonly int $chequeado,
        public readonly ?string $fecha_chequeado,
        public readonly ?string $ip_var,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) $data['id'],
            local_id: (int)$data['local_id'],
            tipo_gasto_id: (int)$data['tipo_gasto_id'],
            periodo_contable_id: (int)$data['periodo_contable_id'],
            especialista_id: $data['especialista_id'] !== null ? (int)$data['especialista_id'] : null,
            responsable_id: $data['responsable_id'] !== null ? (int)$data['responsable_id'] : null,
            operador_id: (int)$data['operador_id'],
            cantidad: (float)$data['cantidad'],
            valor: (float)$data['valor'],
            moneda_id: (int)$data['moneda_id'],
            tasa_cambio: (float)$data['tasa_cambio'],
            tasa_cambio_usd: $data['tasa_cambio_usd'] !== null ? (float)$data['tasa_cambio_usd'] : null,
            chequeado: (int)$data['chequeado'],
            fecha_chequeado: $data['fecha_chequeado'] !== null ? (string)$data['fecha_chequeado'] : null,
            ip_var: $data['ip_var'] !== null ? (string)$data['ip_var'] : null,
        );
    }

    public function attributes(): array
    {
        return [
            'local_id' => $this->local_id,
            'tipo_gasto_id' => $this->tipo_gasto_id,
            'periodo_contable_id' => $this->periodo_contable_id,
            'especialista_id' => $this->especialista_id,
            'responsable_id' => $this->responsable_id,
            'operador_id' => $this->operador_id,
            'cantidad' => $this->cantidad,
            'valor' => $this->valor,
            'moneda_id' => $this->moneda_id,
            'tasa_cambio' => $this->tasa_cambio,
            'tasa_cambio_usd' => $this->tasa_cambio_usd,
            'chequeado' => $this->chequeado,
            'fecha_chequeado' => $this->fecha_chequeado,
            'ip_var' => $this->ip_var,
        ];
    }
}