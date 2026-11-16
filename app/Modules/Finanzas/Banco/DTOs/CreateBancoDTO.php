<?php

namespace App\Modules\Finanzas\Banco\DTOs;

use Illuminate\Support\Facades\Validator;

class CreateBancoDTO
{
    public function __construct(
        public readonly string $nombre,
        public readonly string $alias,
        public readonly string $municipio_id,
        public readonly string $codigo_swift,
        public readonly ?string $direccion,
        public readonly ?string $telefonos,
        public readonly bool $activo,
    ) {}

    public static function fromArray(array $data): self
    {
        $data = Validator::make(
            $data,
            [
                'municipio_id' => [
                    'required',
                    'integer',
                    'min:1',
                ],

                'codigo_swift' => [
                    'required',
                    'string',
                    'max:20',
                ],

                'nombre' => [
                    'required',
                    'string',
                    'max:100',
                ],

                'alias' => [
                    'required',
                    'string',
                    'max:50',
                ],

                'direccion' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'telefonos' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'activo' => [
                    'required',
                    'boolean',
                ],
            ],
            [

                'municipio_id.required' => 'Debe indicar el municipio.',

                'nombre.required' => 'El Nombre es obligatorio.',
                'nombre.max' => 'El Nombre muy largo.',

                'alias.max' => 'El Alias muy largo.',

                'codigo_swift.required' => 'El SWIFT es obligatorio.',
                'codigo_swift.max' => 'SWIFT muy largo.',

                'direccion.max' => 'Dirección muy larga.',

                'telefonos.max' => 'Demasiados Teléfonos.',

                'activo.required' => 'Debe indicarse el estado.',
                'activo.boolean' => 'El estado no es válido.',
            ]
        )->validate();

        return new self(
            nombre: (string)$data['nombre'],
            alias: (string)$data['alias'],
            municipio_id: (int)$data['municipio_id'],
            codigo_swift: (string)$data['codigo_swift'],
            direccion: $data['direccion'] !== null ? (string)$data['direccion'] : null,
            telefonos: $data['telefonos'] !== null ? (string)$data['telefonos'] : null,
            activo: (bool)$data['activo'],
        );
    }

    public function toArray(): array
    {
        return [
            'nombre' => $this->nombre,
            'alias' => $this->alias,
            'municipio_id' => $this->municipio_id,
            'codigo_swift' => $this->codigo_swift,
            'direccion' => $this->direccion,
            'telefonos' => $this->telefonos,
            'activo' => $this->activo,
        ];
    }
}