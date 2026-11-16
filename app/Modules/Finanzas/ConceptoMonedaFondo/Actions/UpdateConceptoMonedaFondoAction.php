<?php

namespace App\Modules\Finanzas\ConceptoMonedaFondo\Actions;

use Illuminate\Support\Facades\DB;
use App\Models\ConceptoMonedaFondo;
use App\Modules\Finanzas\ConceptoMonedaFondo\DTOs\UpdateConceptoMonedaFondoDTO;

class UpdateConceptoMonedaFondoAction
{
    public function __invoke(UpdateConceptoMonedaFondoDTO $dto): ConceptoMonedaFondo {

        return DB::transaction(
            function () use ($dto) {

                $model = ConceptoMonedaFondo::findOrFail($dto->id);

                $model->update(
                    $dto->toArray()
                );

                $model->refresh();

                /*
                |--------------------------------------------------------------------------
                | Future hooks
                |--------------------------------------------------------------------------
                */

                // app(ConceptoMonedaFondoService::class)
                //     ->afterUpdate($model);

                return $model;
            }
        );
    }
}
