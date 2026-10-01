<?php

namespace App\Modules\Gastos\GastoPago\Actions;

use Illuminate\Support\Facades\DB;
use App\Models\GastoPago;
use App\Modules\Gastos\GastoPago\DTOs\UpdateGastoPagoDTO;

class UpdateGastoPagoAction
{
    public function __invoke(UpdateGastoPagoDTO $dto): GastoPago {

        return DB::transaction(
            function () use ($dto) {

                $model = GastoPago::findOrFail($dto->id);

                $model->update(
                    $dto->attributes()
                );

                $model->refresh();

                /*
                |--------------------------------------------------------------------------
                | Future hooks
                |--------------------------------------------------------------------------
                */

                // app(GastoPagoService::class)
                //     ->afterUpdate($model);

                return $model;
            }
        );
    }
}
