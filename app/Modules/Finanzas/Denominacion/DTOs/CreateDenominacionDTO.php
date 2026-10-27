<?php

namespace App\Modules\Finanzas\Denominacion\DTOs;

class CreateDenominacionDTO
{
    public function __construct(
        public readonly int $moneda_id,
        public readonly int $id,
        public readonly string $valor,
        public readonly bool $activa,
        public readonly ?string $color_bg,
        public readonly ?string $color_text,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            moneda_id: (int) $data['moneda_id'],
            id: (int) $data['id'],
            valor: (float)$data['valor'],
            activa: (bool)$data['activa'],
            color_bg: $data['color_bg'] !== null ? (string)$data['color_bg'] : null,
            color_text: $data['color_text'] !== null ? (string)$data['color_text'] : null,
        );
    }

    public function toArray(): array
    {
        return [
            'moneda_id' => $this->moneda_id,
            'valor' => $this->valor,
            'activa' => $this->activa,
            'color_bg' => $this->color_bg,
            'color_text' => $this->color_text,
        ];
    }
}