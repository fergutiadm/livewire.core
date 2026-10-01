<?php

namespace App\Modules\Gastos\Gasto\Actions;

use Illuminate\Support\Facades\DB;
use App\Models\Gasto;
use App\Modules\Gastos\Gasto\DTOs\DeleteGastoDTO;

class DeleteGastoAction
{
    public function __invoke(
        DeleteGastoDTO $dto
    ): bool {

        return DB::transaction(
            function () use ($dto) {

                $model = Gasto::findOrFail(
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

                // app(GastoService::class)
                //     ->beforeDelete($model);

                return (bool) $model->delete();
            }
        );
    }
}