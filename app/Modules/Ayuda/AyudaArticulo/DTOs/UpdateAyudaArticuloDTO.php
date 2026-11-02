<?php

namespace App\Modules\Ayuda\AyudaArticulo\DTOs;

use Illuminate\Support\Facades\Validator;

class UpdateAyudaArticuloDTO
{
    public function __construct(
        public readonly int $id,
        public readonly int $categoria_id,
        public readonly string $titulo,
        public readonly string $slug,
        public readonly ?string $resumen,
        public readonly string $contenido,
        public readonly ?string $contexto,
        public readonly int $orden_visual,
    ) {}

    public static function fromArray(array $data): self
    {
        $data = Validator::make($data, [
            'id' => ['required', 'integer', 'min:1'],
            'categoria_id' => ['required', 'integer', 'min:1'],
            'titulo' => ['required', 'string', 'max:200'],
            'slug' => ['required', 'string', 'max:220'],
            'resumen' => ['nullable', 'string'],
            'contenido' => ['present', 'string'],
            'contexto' => ['nullable', 'string', 'max:160'],
            'orden_visual' => ['required', 'integer', 'min:0'],
        ])->validate();

        return new self(
            id: (int) $data['id'],
            categoria_id: (int) $data['categoria_id'],
            titulo: trim($data['titulo']),
            slug: trim($data['slug']),
            resumen: $data['resumen'] ?? null,
            contenido: $data['contenido'],
            contexto: $data['contexto'] ?? null,
            orden_visual: (int) $data['orden_visual'],
        );
    }

    public function attributes(): array
    {
        return [
            'categoria_id' => $this->categoria_id,
            'titulo' => $this->titulo,
            'slug' => $this->slug,
            'resumen' => $this->resumen,
            'contenido' => $this->contenido,
            'contexto' => $this->contexto,
            'orden_visual' => $this->orden_visual,
        ];
    }
}
