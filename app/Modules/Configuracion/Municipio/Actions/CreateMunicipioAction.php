<?php

namespace App\Modules\Configuracion\Municipio\Actions;

use Illuminate\Support\Facades\DB;
use App\Models\Municipio;
use App\Modules\Configuracion\Municipio\DTOs\CreateMunicipioDTO;

class CreateMunicipioAction
{
    public function __invoke(
        CreateMunicipioDTO $dto
    ): Municipio {

        return DB::transaction(
            function () use ($dto) {

                $model = Municipio::create(
                    $dto->toArray()
                );

                /*
                |--------------------------------------------------------------------------
                | Future hooks
                |--------------------------------------------------------------------------
                */

                // app(MunicipioService::class)
                //     ->afterCreate($model);

                return $model;
            }
        );
    }
}