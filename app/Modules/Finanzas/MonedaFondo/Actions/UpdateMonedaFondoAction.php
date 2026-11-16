<?php

namespace App\Modules\Finanzas\MonedaFondo\Actions;

use App\Models\MonedaFondo;
use App\Modules\Finanzas\MonedaFondo\DTOs\UpdateMonedaFondoDTO;
use Illuminate\Validation\ValidationException;

class UpdateMonedaFondoAction
{
    public function __invoke(
        UpdateMonedaFondoDTO $dto
    ): MonedaFondo {
        throw ValidationException::withMessages([
            'movimiento' =>
                'Los movimientos financieros confirmados no pueden modificarse. '
                . 'La corrección debe realizarse mediante un movimiento compensatorio.',
        ]);
    }
}