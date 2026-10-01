<?php

namespace App\Modules\Gastos\Gasto\Actions;

use Illuminate\Support\Facades\DB;
use App\Models\Gasto;
use App\Modules\Gastos\Gasto\DTOs\UpdateGastoDTO;

class UpdateGastoAction
{
    public function __invoke(UpdateGastoDTO $dto): Gasto {

        return DB::transaction(
            function () use ($dto) {

                $model = Gasto::findOrFail($dto->id);

                $model->update(
                    $dto->attributes()
                );

                $model->refresh();

                /*
                |--------------------------------------------------------------------------
                | Future hooks
                |--------------------------------------------------------------------------
                */

                // app(GastoService::class)
                //     ->afterUpdate($model);

                return $model;
            }
        );
    }
}
