<?php

namespace App\Modules\Gastos\GastoPago\Actions;

use Illuminate\Support\Facades\DB;
use App\Models\GastoPago;
use App\Modules\Gastos\GastoPago\DTOs\CreateGastoPagoDTO;

class CreateGastoPagoAction
{
    public function __invoke(
        CreateGastoPagoDTO $dto
    ): GastoPago {

        return DB::transaction(
            function () use ($dto) {

                $model = GastoPago::create(
                    $dto->toArray()
                );

                /*
                |--------------------------------------------------------------------------
                | Future hooks
                |--------------------------------------------------------------------------
                */

                // app(GastoPagoService::class)
                //     ->afterCreate($model);

                return $model;
            }
        );
    }
}