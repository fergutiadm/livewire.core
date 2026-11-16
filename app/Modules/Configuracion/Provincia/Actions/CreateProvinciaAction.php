<?php

namespace App\Modules\Configuracion\Provincia\Actions;

use Illuminate\Support\Facades\DB;
use App\Models\Provincia;
use App\Modules\Configuracion\Provincia\DTOs\CreateProvinciaDTO;

class CreateProvinciaAction
{
    public function __invoke(
        CreateProvinciaDTO $dto
    ): Provincia {

        return DB::transaction(
            function () use ($dto) {

                $model = Provincia::create(
                    $dto->toArray()
                );

                /*
                |--------------------------------------------------------------------------
                | Future hooks
                |--------------------------------------------------------------------------
                */

                // app(ProvinciaService::class)
                //     ->afterCreate($model);

                return $model;
            }
        );
    }
}