<?php

namespace App\Modules\Gastos\GastoPago\Actions;

use Illuminate\Support\Facades\DB;
use App\Models\GastoPago;
use App\Modules\Gastos\GastoPago\DTOs\DeleteGastoPagoDTO;

class DeleteGastoPagoAction
{
    public function __invoke(
        DeleteGastoPagoDTO $dto
    ): bool {

        return DB::transaction(
            function () use ($dto) {

                $model = GastoPago::findOrFail(
                    $dto->id
                );

                /*
                |--------------------------------------------------------------------------
                | Future business rules
                |--------------------------------------------------------------------------
                */

                /*
                |--------------------------------------------------------------------------
                | Future hooks
                |--------------------------------------------------------------------------
                */

                // app(GastoPagoService::class)
                //     ->beforeDelete($model);

                return (bool) $model->delete();
            }
        );
    }
}