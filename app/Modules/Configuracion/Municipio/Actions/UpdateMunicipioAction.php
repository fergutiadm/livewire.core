<?php

namespace App\Modules\Configuracion\Municipio\Actions;

use Illuminate\Support\Facades\DB;
use App\Models\Municipio;
use App\Modules\Configuracion\Municipio\DTOs\UpdateMunicipioDTO;

class UpdateMunicipioAction
{
    public function __invoke(UpdateMunicipioDTO $dto): Municipio {

        return DB::transaction(
            function () use ($dto) {

                $model = Municipio::findOrFail($dto->id);

                $model->update(
                    $dto->attributes()
                );

                $model->refresh();

                /*
                |--------------------------------------------------------------------------
                | Future hooks
                |--------------------------------------------------------------------------
                */

                // app(MunicipioService::class)
                //     ->afterUpdate($model);

                return $model;
            }
        );
    }
}
