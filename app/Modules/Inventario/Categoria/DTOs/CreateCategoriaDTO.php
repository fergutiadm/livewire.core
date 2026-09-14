<?php

namespace App\Modules\Inventario\Categoria\DTOs;

class CreateCategoriaDTO
{
    public function __construct(
        public readonly ?int $local_id,
        public readonly string $nombre,
        public readonly ?string $descripcion,
        public readonly int $orden_visual,
        public readonly float $porciento_descuento,
        public readonly ?string $imagen_minimalista,
        public readonly ?string $icono,
        public readonly ?string $icono_secundario,
        public readonly ?string $color_bg,
        public readonly ?string $color_text,
        public readonly ?string $color_badge,
    ) {}

    public static function fromArray(
        array $data
    ): self {
        return new self(
            local_id:
                $data['local_id'] !== null
                    ? (int) $data['local_id']
                    : null,

            nombre:
                (string) $data['nombre'],

            descripcion:
                $data['descripcion'] !== null
                    ? (string) $data['descripcion']
                    : null,

            orden_visual:
                (int) $data['orden_visual'],

            porciento_descuento:
                (float) $data['porciento_descuento'],

            imagen_minimalista:
                $data['imagen_minimalista'] !== null
                    ? (string) $data['imagen_minimalista']
                    : null,

            icono:
                $data['icono'] !== null
                    ? (string) $data['icono']
                    : null,

            icono_secundario:
                $data['icono_secundario'] !== null
                    ? (string) $data['icono_secundario']
                    : null,

            color_bg:
                $data['color_bg'] !== null
                    ? (string) $data['color_bg']
                    : null,

            color_text:
                $data['color_text'] !== null
                    ? (string) $data['color_text']
                    : null,

            color_badge:
                $data['color_badge'] !== null
                    ? (string) $data['color_badge']
                    : null,
        );
    }

    public function toArray(): array
    {
        return [
            'local_id' =>
                $this->local_id,

            'nombre' =>
                $this->nombre,

            'descripcion' =>
                $this->descripcion,

            'orden_visual' =>
                $this->orden_visual,

            'porciento_descuento' =>
                $this->porciento_descuento,

            'imagen_minimalista' =>
                $this->imagen_minimalista,

            'icono' =>
                $this->icono,

            'icono_secundario' =>
                $this->icono_secundario,

            'color_bg' =>
                $this->color_bg,

            'color_text' =>
                $this->color_text,

            'color_badge' =>
                $this->color_badge,
        ];
    }
}
