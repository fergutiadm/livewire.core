<?php

namespace App\Modules\Finanzas\ConceptoMonedaFondo\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Models\ConceptoMonedaFondo;
use App\Modules\Finanzas\ConceptoMonedaFondo\DTOs\DeleteConceptoMonedaFondoDTO;

class DeleteConceptoMonedaFondoAction
{
    public function __invoke(
        DeleteConceptoMonedaFondoDTO $dto
    ): bool {

        return DB::transaction(function () use ($dto) {

            $model = ConceptoMonedaFondo::findOrFail($dto->id);

            if ($model->monedaFondos()->exists()) {
                throw ValidationException::withMessages([
                    'id' => 'No se puede eliminar este concepto porque ya tiene movimientos financieros asociados. Puede desactivarlo en su lugar.',
                ]);
            }

            return (bool) $model->delete();
        });
    }
}