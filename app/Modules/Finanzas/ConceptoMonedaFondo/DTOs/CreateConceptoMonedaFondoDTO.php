<?php

namespace App\Modules\Finanzas\ConceptoMonedaFondo\DTOs;

use App\Models\ConceptoMonedaFondo;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CreateConceptoMonedaFondoDTO
{
    public function __construct(
        public readonly string $nombre,
        public readonly string $tipo,
        public readonly string $slug,
        public readonly bool $entrada_salida,
        public readonly bool $activo,
    ) {}

    public static function fromArray(array $data): self
    {
        $data = Validator::make(
            $data,
            [
                'nombre' => ['required', 'string', 'max:150'],
                'tipo' => [
                    'nullable',
                    'string',
                    Rule::in([
                        ConceptoMonedaFondo::TIPO_ESTANDAR,
                        ConceptoMonedaFondo::TIPO_OPERACIONAL,
                    ]),],
                'slug' => [
                    'required',
                    'string',
                    'max:160',
                    'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                ],
                'entrada_salida' => ['required', 'boolean'],
                'activo' => ['required', 'boolean'],
            ],
            [
                'nombre.required' =>
                    'El nombre del concepto es obligatorio.',

                'nombre.string' =>
                    'El nombre del concepto debe ser texto.',

                'nombre.max' =>
                    'El nombre del concepto no puede superar los 150 caracteres.',

                'tipo.in' => 'El tipo seleccionado no es válido.',

                'slug.required' =>
                    'No se pudo generar el identificador del concepto.',

                'slug.string' =>
                    'El identificador del concepto debe ser texto.',

                'slug.max' =>
                    'El identificador del concepto no puede superar los 160 caracteres.',

                'slug.regex' =>
                    'El identificador del concepto contiene caracteres no permitidos.',

                'entrada_salida.required' =>
                    'Debe indicarse si el concepto corresponde a una entrada o una salida.',

                'entrada_salida.boolean' =>
                    'El indicador de entrada o salida no es válido.',

                'activo.required' =>
                    'Debe indicarse el estado del concepto.',

                'activo.boolean' =>
                    'El estado del concepto no es válido.',
            ]
        )->validate();

        return new self(
            nombre: (string)$data['nombre'],
            tipo: (string)$data['tipo'],
            slug: (string)$data['slug'],
            entrada_salida: (bool)$data['entrada_salida'],
            activo: (bool)$data['activo'],
        );
    }

    public function toArray(): array
    {
        return [
            'nombre' => $this->nombre,
            'tipo' => $this->tipo,
            'slug' => $this->slug,
            'entrada_salida' => $this->entrada_salida,
            'activo' => $this->activo,
        ];
    }
}