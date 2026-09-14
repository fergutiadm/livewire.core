<?php

namespace App\Modules\Finanzas\TarjetaMagnetica\Actions;

use Illuminate\Support\Facades\DB;
use App\Models\TarjetaMagnetica;
use App\Modules\Finanzas\TarjetaMagnetica\DTOs\UpdateTarjetaMagneticaDTO;

class UpdateTarjetaMagneticaAction
{
    public function __invoke(UpdateTarjetaMagneticaDTO $dto): TarjetaMagnetica {

        return DB::transaction(
            function () use ($dto) {

                $model = TarjetaMagnetica::findOrFail($dto->id);

                $model->update(
                    $dto->attributes()
                );

                $model->refresh();

                /*
                |--------------------------------------------------------------------------
                | Future hooks
                |--------------------------------------------------------------------------
                */

                // app(TarjetaMagneticaService::class)
                //     ->afterUpdate($model);

                return $model;
            }
        );
    }
}
