<?php

namespace App\Modules\Gastos\TipoGasto\Actions;

use Illuminate\Support\Facades\DB;
use App\Models\TipoGasto;
use App\Modules\Gastos\TipoGasto\DTOs\CreateTipoGastoDTO;

class CreateTipoGastoAction
{
    public function __invoke(
        CreateTipoGastoDTO $dto
    ): TipoGasto {

        return DB::transaction(
            function () use ($dto) {

                $model = TipoGasto::create(
                    $dto->toArray()
                );

                /*
                |--------------------------------------------------------------------------
                | Future hooks
                |--------------------------------------------------------------------------
                */

                // app(TipoGastoService::class)
                //     ->afterCreate($model);

                return $model;
            }
        );
    }
}