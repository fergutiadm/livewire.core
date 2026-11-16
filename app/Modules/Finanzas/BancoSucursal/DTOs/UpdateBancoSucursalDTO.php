<?php

namespace App\Modules\Finanzas\BancoSucursal\DTOs;

use Illuminate\Support\Facades\Validator;

class UpdateBancoSucursalDTO
{
    public function __construct(
        public readonly int $id,
        public readonly int $banco_id,
        public readonly int $municipio_id,
        public readonly string $nombre,
        public readonly string $codigo,
        public readonly ?string $direccion,
        public readonly ?string $telefonos,
        public readonly bool $activa,
    ) {}

    public static function fromArray(array $data): self
    {
        $data = Validator::make(
            $data,
            [
                'id' => [
                    'required',
                    'integer',
                    'min:1',
                ],

                'municipio_id' => [
                    'required',
                    'integer',
                    'min:1',
                ],

                'banco_id' => [
                    'required',
                    'integer',
                    'min:1',
                ],

                'codigo' => [
                    'required',
                    'string',
                    'max:150',
                ],

                'nombre' => [
                    'required',
                    'string',
                    'max:150',
                ],

                'direccion' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'telefonos' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'activa' => [
                    'required',
                    'boolean',
                ],
            ],
            [
                'id.required' => 'No se indicó el banco que se desea actualizar.',
                'id.integer' => 'El identificador del banco no es válido.',
                'id.min' => 'El identificador del banco no es válido.',

                'municipio_id.required' => 'Debe indicar el municipio.',

                'banco_id.required' => 'Debe indicar el banco.',

                'nombre.required' => 'El Nombre es obligatorio.',
                'nombre.max' => 'Código muy largo.',

                'codigo.required' => 'El Código es obligatoria.',
                'codigo.max' => 'Código muy largo.',

                'telefonos.max' => 'Demasiados Teléfonos.',

                'activa.required' => 'Debe indicarse el estado.',
                'activa.boolean' => 'El estado no es válido.',
            ]
        )->validate();

        return new self(
            id: (int) $data['id'],
            banco_id: (int) $data['banco_id'],
            municipio_id: (int) $data['municipio_id'],
            nombre: (string)$data['nombre'],
            codigo: (string)$data['codigo'],
            direccion: $data['direccion'] !== null ? (string)$data['direccion'] : null,
            telefonos: $data['telefonos'] !== null ? (string)$data['telefonos'] : null,
            activa: (bool)$data['activa'],
        );
    }

    public function attributes(): array
    {
        return [
            'banco_id' => $this->banco_id,
            'municipio_id' => $this->municipio_id,
            'nombre' => $this->nombre,
            'codigo' => $this->codigo,
            'direccion' => $this->direccion,
            'telefonos' => $this->telefonos,
            'activa' => $this->activa,
        ];
    }
}