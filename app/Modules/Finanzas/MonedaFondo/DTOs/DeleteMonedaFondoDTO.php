<?php

namespace App\Modules\Finanzas\MonedaFondo\DTOs;

use Illuminate\Support\Facades\Validator;

class DeleteMonedaFondoDTO
{
    public function __construct(
        public readonly int $id
    ) {}

    public static function fromArray(array $data): self
    {
        $data = Validator::make($data, [
            'id' => [
                'required',
                'integer',
                'min:1',
            ],
        ], [
            'id.required' => 'Debe indicar el movimiento financiero.',
            'id.integer' => 'El identificador del movimiento no es válido.',
            'id.min' => 'El identificador del movimiento no es válido.',
        ])->validate();

        return new self(
            id: (int) $data['id'],
        );
    }
}