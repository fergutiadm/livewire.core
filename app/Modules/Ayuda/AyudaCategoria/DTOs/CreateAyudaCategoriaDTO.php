<?php

namespace App\Modules\Ayuda\AyudaCategoria\DTOs;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CreateAyudaCategoriaDTO
{
    public function __construct(
        public readonly string $nombre,
        public readonly string $slug,
        public readonly ?string $descripcion,
        public readonly int $orden_visual,
        public readonly bool $activa,
    ) {}

    public static function fromArray(array $data): self
    {
        $data = Validator::make($data, [
            'nombre' => ['required', 'string', 'max:150'],
            'slug' => ['required', 'string', 'max:160'],
            'descripcion' => ['nullable', 'string'],
            'orden_visual' => ['sometimes', 'integer'],
            'activa' => ['sometimes', 'boolean'],
        ])->validate();

        return new self(
            nombre: trim($data['nombre']),
            slug: trim($data['slug']),
            descripcion: $data['descripcion'] ?? null,
            orden_visual: (int) ($data['orden_visual'] ?? 0),
            activa: (bool) ($data['activa'] ?? true),
        );
    }

    public function toArray(): array
    {
        return [
            'nombre' => $this->nombre,
            'slug' => $this->slug,
            'descripcion' => $this->descripcion,
            'orden_visual' => $this->orden_visual,
            'activa' => $this->activa,
        ];
    }
}
