<?php

namespace App\Modules\Finanzas\CuentaBancaria\DTOs;

class CreateCuentaBancariaDTO
{
    public function __construct(
        public readonly int $banco_id,
        public readonly int $moneda_id,
        public readonly string $numero_cuenta,
        public string $titular,
        public ?string $descripcion,
        public readonly bool $activa,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            banco_id: (int)$data['banco_id'],
            moneda_id: (int)$data['moneda_id'],
            numero_cuenta: (string)$data['numero_cuenta'],
            titular: (string)$data['titular'],
            descripcion: (string)$data['descripcion']??'',
            activa: (bool)$data['activa'],
        );
    }

    public function toArray(): array
    {
        return [
            'banco_id' => $this->banco_id,
            'moneda_id' => $this->moneda_id,
            'numero_cuenta' => $this->numero_cuenta,
            'titular' => $this->titular,
            'descripcion' => $this->descripcion,
            'activa' => $this->activa,
        ];
    }
}