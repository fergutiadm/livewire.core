<?php

namespace App\Modules\Gastos\GastoPago\DTOs;

class CreateGastoPagoDTO
{
    public function __construct(
        public readonly int $gasto_id,
        public readonly string $metodo,
        public readonly ?int $tarjeta_magnetica_id,
        public readonly ?string $numero_tarjeta,
        public readonly int $moneda_id,
        public readonly float $cantidad,
        public readonly float $tasa_cambio,
        public readonly string $fecha_pago,
        public readonly int $operador_id,
        public readonly ?string $descripcion,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            gasto_id: (int) $data['gasto_id'],
            metodo: (string) $data['metodo'],
            tarjeta_magnetica_id: $data['tarjeta_magnetica_id'] !== null
                ? (int) $data['tarjeta_magnetica_id']
                : null,
            numero_tarjeta: $data['numero_tarjeta'] !== null
                ? (string) $data['numero_tarjeta']
                : null,
            moneda_id: (int) $data['moneda_id'],
            cantidad: (float) $data['cantidad'],
            tasa_cambio: (float) $data['tasa_cambio'],
            fecha_pago: (string) $data['fecha_pago'],
            operador_id: (int) $data['operador_id'],
            descripcion: $data['descripcion'] !== null
                ? (string) $data['descripcion']
                : null,
        );
    }

    public function toArray(): array
    {
        return [
            'gasto_id' => $this->gasto_id,
            'metodo' => $this->metodo,
            'tarjeta_magnetica_id' => $this->tarjeta_magnetica_id,
            'numero_tarjeta' => $this->numero_tarjeta,
            'moneda_id' => $this->moneda_id,
            'cantidad' => $this->cantidad,
            'tasa_cambio' => $this->tasa_cambio,
            'fecha_pago' => $this->fecha_pago,
            'operador_id' => $this->operador_id,
            'descripcion' => $this->descripcion,
        ];
    }
}