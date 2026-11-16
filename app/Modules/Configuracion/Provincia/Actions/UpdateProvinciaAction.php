<?php

namespace App\Modules\Configuracion\Provincia\Actions;

use Illuminate\Support\Facades\DB;
use App\Models\Provincia;
use App\Modules\Configuracion\Provincia\DTOs\UpdateProvinciaDTO;

class UpdateProvinciaAction
{
    public function __invoke(UpdateProvinciaDTO $dto): Provincia {

        return DB::transaction(
            function () use ($dto) {

                $model = Provincia::findOrFail($dto->id);

                $model->update(
                    $dto->attributes()
                );

                $model->refresh();

                /*
                |--------------------------------------------------------------------------
                | Future hooks
                |--------------------------------------------------------------------------
                */

                // app(ProvinciaService::class)
                //     ->afterUpdate($model);

                return $model;
            }
        );
    }
}
