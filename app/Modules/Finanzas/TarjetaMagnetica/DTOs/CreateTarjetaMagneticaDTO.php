<?php

namespace App\Modules\Finanzas\TarjetaMagnetica\DTOs;

class CreateTarjetaMagneticaDTO
{
    public function __construct(
        public readonly int $moneda_id,
        public readonly string $numero,
        public readonly string $propietario,
        public readonly ?string $color_bg,
        public readonly ?string $color_text,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            moneda_id: (int)$data['moneda_id'],
            numero: (string)$data['numero'],
            propietario: (string)$data['propietario'],
            color_bg: $data['color_bg'] !== null ? (string)$data['color_bg'] : null,
            color_text: $data['color_text'] !== null ? (string)$data['color_text'] : null,
        );
    }

    public function toArray(): array
    {
        return [
            'moneda_id' => $this->moneda_id,
            'numero' => $this->numero,
            'propietario' => $this->propietario,
            'color_bg' => $this->color_bg,
            'color_text' => $this->color_text,
        ];
    }
}
