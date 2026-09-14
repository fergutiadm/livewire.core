<?php

namespace App\Modules\Finanzas\Moneda\DTOs;

class UpdateMonedaDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $codigo,
        public readonly string $nombre,
        public readonly ?string $simbolo,
        public readonly bool $es_principal,
        public readonly float $tasa_cambio,
        public readonly bool $activa,
        public readonly ?string $color_bg,
        public readonly ?string $color_text,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) $data['id'],
            codigo: (string)$data['codigo'],
            nombre: (string)$data['nombre'],
            simbolo: $data['simbolo'] !== null ? (string)$data['simbolo'] : null,
            es_principal: (bool)$data['es_principal'],
            tasa_cambio: (float)$data['tasa_cambio'],
            activa: (bool)$data['activa'],
            color_bg: $data['color_bg'] !== null ? (string)$data['color_bg'] : null,
            color_text: $data['color_text'] !== null ? (string)$data['color_text'] : null,
        );
    }

    public function attributes(): array
    {
        return [
            'codigo' => $this->codigo,
            'nombre' => $this->nombre,
            'simbolo' => $this->simbolo,
            'es_principal' => $this->es_principal,
            'tasa_cambio' => $this->tasa_cambio,
            'activa' => $this->activa,
            'color_bg' => $this->color_bg,
            'color_text' => $this->color_text,
        ];
    }
}