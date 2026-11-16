<?php

namespace App\Modules\Finanzas\MonedaFondo\Actions;

use App\Models\MonedaFondo;
use App\Modules\Finanzas\MonedaFondo\DTOs\DeleteMonedaFondoDTO;
use Illuminate\Validation\ValidationException;

class DeleteMonedaFondoAction
{
    public function __invoke(
        DeleteMonedaFondoDTO $dto
    ): bool {
        MonedaFondo::findOrFail($dto->id);

        throw ValidationException::withMessages([
            'delete' =>
                'Los movimientos financieros confirmados no pueden eliminarse. '
                . 'La corrección debe realizarse mediante un movimiento compensatorio.',
        ]);
    }
}