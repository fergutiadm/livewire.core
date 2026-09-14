<?php

namespace App\Modules\Finanzas\TarjetaMagnetica\Actions;

use Illuminate\Support\Facades\DB;
use App\Models\TarjetaMagnetica;
use App\Modules\Finanzas\TarjetaMagnetica\DTOs\CreateTarjetaMagneticaDTO;

class CreateTarjetaMagneticaAction
{
    public function __invoke(
        CreateTarjetaMagneticaDTO $dto
    ): TarjetaMagnetica {

        return DB::transaction(
            function () use ($dto) {

                $model = TarjetaMagnetica::create(
                    $dto->toArray()
                );

                /*
                |--------------------------------------------------------------------------
                | Future hooks
                |--------------------------------------------------------------------------
                */

                // app(TarjetaMagneticaService::class)
                //     ->afterCreate($model);

                return $model;
            }
        );
    }
}