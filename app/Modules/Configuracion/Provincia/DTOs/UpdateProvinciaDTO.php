<?php

namespace App\Modules\Configuracion\Provincia\DTOs;

use Illuminate\Support\Facades\Validator;

class UpdateProvinciaDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $nombre,
        public readonly string $slug,
        public readonly int $activa,
    ) {}

    public static function fromArray(array $data): self
    {
        $data = Validator::make(
            $data,
            [
                'id' => ['required', 'integer', 'min:1'],
                'nombre' => ['required', 'string', 'max:150'],
                'slug' => [
                    'required',
                    'string',
                    'max:160',
                    'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                ],
                'activa' => ['required', 'boolean'],
            ],
            [
                'id.required' => 'No se indicó el provincia que se desea actualizar.',
                'id.integer' => 'El identificador del provincia no es válido.',
                'id.min' => 'El identificador del provincia no es válido.',

                'nombre.required' => 'El nombre del provincia es obligatorio.',
                'nombre.string' => 'El nombre del provincia debe ser texto.',
                'nombre.max' => 'El nombre del provincia muy largo.',

                'slug.required' => 'No se pudo generar el identificador del provincia.',
                'slug.string' => 'El identificador del provincia debe ser texto.',
                'slug.max' => 'El identificador del provincia no puede superar los 160 caracteres.',
                'slug.regex' => 'El identificador del provincia contiene caracteres no permitidos.',

                'activa.required' => 'Debe indicarse el estado del provincia.',
                'activa.boolean' => 'El estado del provincia no es válido.',
            ]
        )->validate();

        return new self(
            id: (int) $data['id'],
            nombre: (string)$data['nombre'],
            slug: (string)$data['slug'],
            activa: (int)$data['activa'],
        );
    }

    public function attributes(): array
    {
        return [
            'nombre' => $this->nombre,
            'slug' => $this->slug,
            'activa' => $this->activa,
        ];
    }
}