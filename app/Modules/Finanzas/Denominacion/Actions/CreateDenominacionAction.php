<?php

namespace App\Modules\Finanzas\Denominacion\Actions;

use Illuminate\Support\Facades\DB;
use App\Models\Denominacion;
use App\Modules\Finanzas\Denominacion\DTOs\CreateDenominacionDTO;

class CreateDenominacionAction
{
    public function __invoke(
        CreateDenominacionDTO $dto
    ): Denominacion {

        return DB::transaction(
            function () use ($dto) {

                $model = Denominacion::create(
                    $dto->toArray()
                );

                /*
                |--------------------------------------------------------------------------
                | Future hooks
                |--------------------------------------------------------------------------
                */

                // app(DenominacionService::class)
                //     ->afterCreate($model);

                return $model;
            }
        );
    }
}