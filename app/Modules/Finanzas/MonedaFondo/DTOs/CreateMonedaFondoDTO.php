<?php

namespace App\Modules\Finanzas\MonedaFondo\DTOs;

use Illuminate\Support\Facades\Validator;

class CreateMonedaFondoDTO
{
    public function __construct(
        public readonly int $local_id,
        public readonly int $periodo_id,
        public readonly int $moneda_id,
        public readonly int $concepto_id,
        public readonly string $descripcion,
        public readonly string $monto,
        public readonly string $tasa_cambio,
        public readonly array $desglose,
        public readonly string $fecha,
    ) {}

    public static function fromArray(array $data): self
    {
        $data = Validator::make(
            $data,
            [
                'local_id' => [
                    'required',
                    'integer',
                    'min:1',
                ],

                'periodo_id' => [
                    'required',
                    'integer',
                    'min:1',
                ],

                'moneda_id' => [
                    'required',
                    'integer',
                    'min:1',
                ],

                'concepto_id' => [
                    'required',
                    'integer',
                    'min:1',
                ],

                'descripcion' => [
                    'required',
                    'string',
                    'max:255',
                ],

                // DECIMAL(20,2): máximo 18 dígitos enteros.
                'monto' => [
                    'required',
                    'numeric',
                    'gt:0',
                    'regex:/^\d{1,18}(?:\.\d{1,2})?$/',
                ],

                // DECIMAL(20,10): máximo 10 dígitos enteros.
                'tasa_cambio' => [
                    'required',
                    'numeric',
                    'gt:0',
                    'regex:/^\d{1,10}(?:\.\d{1,10})?$/',
                ],

                'desglose' => [
                    'present',
                    'array',
                ],

                'fecha' => [
                    'required',
                    'date_format:Y-m-d',
                ],
            ],
            [
                'local_id.required' => 'Debe indicar el local.',
                'periodo_id.required' => 'Debe indicar el período contable.',
                'moneda_id.required' => 'Debe indicar la moneda.',
                'concepto_id.required' => 'Debe indicar el concepto.',

                'descripcion.required' => 'La descripción es obligatoria.',
                'descripcion.max' => 'La descripción no puede superar los 255 caracteres.',

                'monto.required' => 'El monto es obligatorio.',
                'monto.numeric' => 'El monto debe ser numérico.',
                'monto.gt' => 'El monto debe ser mayor que cero.',
                'monto.regex' => 'El monto admite hasta 18 dígitos enteros y 2 decimales.',

                'tasa_cambio.required' => 'La tasa de cambio es obligatoria.',
                'tasa_cambio.numeric' => 'La tasa de cambio debe ser numérica.',
                'tasa_cambio.gt' => 'La tasa de cambio debe ser mayor que cero.',
                'tasa_cambio.regex' => 'La tasa admite hasta 10 dígitos enteros y 10 decimales.',

                'fecha.required' => 'La fecha es obligatoria.',
                'fecha.date_format' => 'La fecha debe tener el formato YYYY-MM-DD.',
            ]
        )->validate();

        return new self(
            local_id: (int) $data['local_id'],
            periodo_id: (int) $data['periodo_id'],
            moneda_id: (int) $data['moneda_id'],
            concepto_id: (int) $data['concepto_id'],
            descripcion: trim((string) $data['descripcion']),
            monto: (string) $data['monto'],
            tasa_cambio: (string) $data['tasa_cambio'],
            desglose: (array) $data['desglose'],
            fecha: (string) $data['fecha'],
        );
    }

    public function toArray(): array
    {
        return [
            'local_id' => $this->local_id,
            'periodo_id' => $this->periodo_id,
            'moneda_id' => $this->moneda_id,
            'concepto_id' => $this->concepto_id,
            'descripcion' => $this->descripcion,
            'monto' => $this->monto,
            'tasa_cambio' => $this->tasa_cambio,
            'desglose' => $this->desglose,
            'fecha' => $this->fecha,
        ];
    }
}
