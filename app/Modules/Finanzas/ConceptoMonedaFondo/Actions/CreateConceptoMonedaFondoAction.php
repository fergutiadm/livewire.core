<?php

namespace App\Modules\Finanzas\ConceptoMonedaFondo\Actions;

use Illuminate\Support\Facades\DB;
use App\Models\ConceptoMonedaFondo;
use App\Modules\Finanzas\ConceptoMonedaFondo\DTOs\CreateConceptoMonedaFondoDTO;

class CreateConceptoMonedaFondoAction
{
    public function __invoke(
        CreateConceptoMonedaFondoDTO $dto
    ): ConceptoMonedaFondo {

        return DB::transaction(
            function () use ($dto) {

                $model = ConceptoMonedaFondo::create(
                    $dto->toArray()
                );

                /*
                |--------------------------------------------------------------------------
                | Future hooks
                |--------------------------------------------------------------------------
                */

                // app(ConceptoMonedaFondoService::class)
                //     ->afterCreate($model);

                return $model;
            }
        );
    }
}