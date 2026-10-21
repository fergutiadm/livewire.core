<?php

namespace App\Modules\Finanzas\Caja\Actions;

use Illuminate\Support\Facades\DB;
use App\Models\Caja;
use App\Modules\Finanzas\Caja\DTOs\CreateCajaDTO;

class CreateCajaAction
{
    public function __invoke(
        CreateCajaDTO $dto
    ): Caja {

        return DB::transaction(
            function () use ($dto) {

                $model = Caja::create(
                    $dto->toArray()
                );

                /*
                |--------------------------------------------------------------------------
                | Future hooks
                |--------------------------------------------------------------------------
                */

                // app(CajaService::class)
                //     ->afterCreate($model);

                return $model;
            }
        );
    }
}