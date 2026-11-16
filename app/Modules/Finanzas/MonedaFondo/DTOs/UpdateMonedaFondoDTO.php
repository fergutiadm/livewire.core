<?php

namespace App\Modules\Finanzas\MonedaFondo\DTOs;

use Illuminate\Validation\ValidationException;

class UpdateMonedaFondoDTO
{
    private function __construct()
    {
    }

    public static function fromArray(array $data): self
    {
        throw ValidationException::withMessages([
            'movimiento' =>
                'Los movimientos financieros confirmados no pueden modificarse. '
                . 'La corrección debe realizarse mediante un movimiento compensatorio.',
        ]);
    }

    public function toArray(): array
    {
        throw ValidationException::withMessages([
            'movimiento' =>
                'Los movimientos financieros confirmados no pueden modificarse.',
        ]);
    }
}