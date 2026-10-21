<?php

namespace App\Modules\Finanzas\Caja\Actions;

use Illuminate\Support\Facades\DB;
use App\Models\Caja;
use App\Modules\Finanzas\Caja\DTOs\DeleteCajaDTO;

class DeleteCajaAction
{
    public function __invoke(
        DeleteCajaDTO $dto
    ): bool {

        return DB::transaction(
            function () use ($dto) {

                $model = Caja::findOrFail(
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

                // app(CajaService::class)
                //     ->beforeDelete($model);

                return (bool) $model->delete();
            }
        );
    }
}