<?php

namespace App\Modules\Ayuda\AyudaArticulo\DTOs;

use Illuminate\Support\Facades\Validator;

class CreateAyudaArticuloDTO
{
    public function __construct(
        public readonly int $categoria_id,
        public readonly string $titulo,
        public readonly string $slug,
        public readonly ?string $resumen,
        public readonly string $contenido,
        public readonly ?string $contexto,
        public readonly int $orden_visual,
        public readonly bool $publicado,
    ) {}

    public static function fromArray(array $data): self
    {
        $data = Validator::make($data, [
            'categoria_id' => ['required', 'integer', 'min:1'],
            'titulo' => ['required', 'string', 'max:200'],
            'slug' => ['required', 'string', 'max:220'],
            'resumen' => ['nullable', 'string'],
            'contenido' => ['present', 'string'],
            'contexto' => ['nullable', 'string', 'max:160'],
            // 'orden_visual' => ['sometimes', 'integer', 'min:0'],
        ])->validate();

        return new self(
            categoria_id: (int) $data['categoria_id'],
            titulo: trim($data['titulo']),
            slug: trim($data['slug']),
            resumen: $data['resumen'] ?? null,
            contenido: $data['contenido'],
            contexto: $data['contexto'] ?? null,
            orden_visual: (int) ($data['orden_visual'] ?? 0),
            publicado: false,
        );
    }

    public function toArray(): array
    {
        return [
            'categoria_id' => $this->categoria_id,
            'titulo' => $this->titulo,
            'slug' => $this->slug,
            'resumen' => $this->resumen,
            'contenido' => $this->contenido,
            'contexto' => $this->contexto,
            'orden_visual' => $this->orden_visual,
            'publicado' => $this->publicado,
        ];
    }
}
