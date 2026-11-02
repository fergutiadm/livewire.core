<?php

namespace App\Modules\Ayuda\AyudaCategoria\DTOs;

use Illuminate\Support\Facades\Validator;

class UpdateAyudaCategoriaDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $nombre,
        public readonly string $slug,
        public readonly ?string $descripcion,
        public readonly int $orden_visual,
        public readonly bool $activa,
    ) {}

    public static function fromArray(array $data): self
    {
        $data = Validator::make($data, [
            'id' => ['required', 'integer', 'min:1'],
            'nombre' => ['required', 'string', 'max:150'],
            'slug' => ['required', 'string', 'max:160'],
            'descripcion' => ['nullable', 'string'],
            'orden_visual' => ['required', 'integer'],
            'activa' => ['required', 'boolean'],
        ])->validate();

        return new self(
            id: (int) $data['id'],
            nombre: trim($data['nombre']),
            slug: trim($data['slug']),
            descripcion: $data['descripcion'] ?? null,
            orden_visual: (int) $data['orden_visual'],
            activa: (bool) $data['activa'],
        );
    }

    public function attributes(): array
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
