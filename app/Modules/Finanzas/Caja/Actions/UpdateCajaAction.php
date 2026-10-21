<?php

namespace App\Modules\Finanzas\Caja\Actions;

use Illuminate\Support\Facades\DB;
use App\Models\Caja;
use App\Modules\Finanzas\Caja\DTOs\UpdateCajaDTO;

class UpdateCajaAction
{
    public function __invoke(UpdateCajaDTO $dto): Caja {

        return DB::transaction(
            function () use ($dto) {

                $model = Caja::findOrFail($dto->id);

                $model->update(
                    $dto->attributes()
                );

                $model->refresh();

                /*
                |--------------------------------------------------------------------------
                | Future hooks
                |--------------------------------------------------------------------------
                */

                // app(CajaService::class)
                //     ->afterUpdate($model);

                return $model;
            }
        );
    }
}
