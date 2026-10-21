<?php

namespace App\Modules\Admin\Denominacion\Actions;

use Illuminate\Support\Facades\DB;
use App\Models\Denominacion;
use App\Modules\Admin\Denominacion\DTOs\UpdateDenominacionDTO;

class UpdateDenominacionAction
{
    public function __invoke(UpdateDenominacionDTO $dto): Denominacion {

        return DB::transaction(
            function () use ($dto) {

                $model = Denominacion::findOrFail($dto->id);

                $model->update(
                    $dto->attributes()
                );

                $model->refresh();

                /*
                |--------------------------------------------------------------------------
                | Future hooks
                |--------------------------------------------------------------------------
                */

                // app(DenominacionService::class)
                //     ->afterUpdate($model);

                return $model;
            }
        );
    }
}
