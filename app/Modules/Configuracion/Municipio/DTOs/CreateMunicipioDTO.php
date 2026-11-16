<?php

namespace App\Modules\Configuracion\Municipio\DTOs;

use Illuminate\Support\Facades\Validator;

class CreateMunicipioDTO
{
    public function __construct(
        public readonly int $provincia_id,
        public readonly string $nombre,
        public readonly string $slug,
        public readonly int $activo,
    ) {}

    public static function fromArray(array $data): self
    {
        $data = Validator::make(
            $data,
            [
                'provincia_id' => ['required', 'integer', 'min:1'],
                'nombre' => ['required', 'string', 'max:150'],
                'slug' => [
                    'required',
                    'string',
                    'max:160',
                    'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                ],
                'activo' => ['required', 'boolean'],
            ],
            [
                'provincia_id.required' => 'No se indicó el provincia que se desea actualizar.',
                'provincia_id.integer' => 'El identificador del provincia no es válido.',
                'provincia_id.min' => 'El identificador del provincia no es válido.',

                'nombre.required' => 'El nombre del municipio es obligatorio.',
                'nombre.string' => 'El nombre del municipio debe ser texto.',
                'nombre.max' => 'El nombre del municipio muy largo.',

                'slug.required' => 'No se pudo generar el identificador del municipio.',
                'slug.string' => 'El identificador del municipio debe ser texto.',
                'slug.max' => 'El identificador del municipio no puede superar los 160 caracteres.',
                'slug.regex' => 'El identificador del municipio contiene caracteres no permitidos.',

                'activo.required' => 'Debe indicarse el estado del municipio.',
                'activo.boolean' => 'El estado del municipio no es válido.',
            ]
        )->validate();

        return new self(
            provincia_id: (int)$data['provincia_id'],
            nombre: (string)$data['nombre'],
            slug: (string)$data['slug'],
            activo: (int)$data['activo'],
        );
    }

    public function toArray(): array
    {
        return [
            'provincia_id' => $this->provincia_id,
            'nombre' => $this->nombre,
            'slug' => $this->slug,
            'activo' => $this->activo,
        ];
    }
}