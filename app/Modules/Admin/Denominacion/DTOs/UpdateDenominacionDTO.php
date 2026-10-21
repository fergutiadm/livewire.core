<?php

namespace App\Modules\Admin\Denominacion\DTOs;

class UpdateDenominacionDTO
{
    public function __construct(
        public readonly int $id,
        public readonly int $moneda_id,
        public readonly string $valor,
        public readonly bool $activa,
        public readonly ?string $color_bg,
        public readonly ?string $color_text,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) $data['id'],
            moneda_id: (int) $data['moneda_id'],
            valor: (float)$data['valor'],
            activa: (bool)$data['activa'],
            color_bg: $data['color_bg'] !== null ? (string)$data['color_bg'] : null,
            color_text: $data['color_text'] !== null ? (string)$data['color_text'] : null,
        );
    }

    public function attributes(): array
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