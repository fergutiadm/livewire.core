<?php

namespace App\Modules\Gastos\TipoGasto\Actions;

use Illuminate\Support\Facades\DB;
use App\Models\TipoGasto;
use App\Modules\Gastos\TipoGasto\DTOs\UpdateTipoGastoDTO;

class UpdateTipoGastoAction
{
    public function __invoke(UpdateTipoGastoDTO $dto): TipoGasto {

        return DB::transaction(
            function () use ($dto) {

                $model = TipoGasto::findOrFail($dto->id);

                $model->update(
                    $dto->attributes()
                );

                $model->refresh();

                /*
                |--------------------------------------------------------------------------
                | Future hooks
                |--------------------------------------------------------------------------
                */

                // app(TipoGastoService::class)
                //     ->afterUpdate($model);

                return $model;
            }
        );
    }
}
