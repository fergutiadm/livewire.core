<?php

namespace App\Modules\Gastos\TipoGasto\Actions;

use Illuminate\Support\Facades\DB;
use App\Models\TipoGasto;
use App\Modules\Gastos\TipoGasto\DTOs\DeleteTipoGastoDTO;

class DeleteTipoGastoAction
{
    public function __invoke(
        DeleteTipoGastoDTO $dto
    ): bool {

        return DB::transaction(
            function () use ($dto) {

                $model = TipoGasto::findOrFail(
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

                // app(TipoGastoService::class)
                //     ->beforeDelete($model);

                return (bool) $model->delete();
            }
        );
    }
}