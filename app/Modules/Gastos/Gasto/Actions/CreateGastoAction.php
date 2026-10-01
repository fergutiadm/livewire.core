<?php

namespace App\Modules\Gastos\Gasto\Actions;

use Illuminate\Support\Facades\DB;
use App\Models\Gasto;
use App\Modules\Gastos\Gasto\DTOs\CreateGastoDTO;

class CreateGastoAction
{
    public function __invoke(
        CreateGastoDTO $dto
    ): Gasto {

        return DB::transaction(
            function () use ($dto) {

                $model = Gasto::create(
                    $dto->toArray()
                );

                /*
                |--------------------------------------------------------------------------
                | Future hooks
                |--------------------------------------------------------------------------
                */

                // app(GastoService::class)
                //     ->afterCreate($model);

                return $model;
            }
        );
    }
}