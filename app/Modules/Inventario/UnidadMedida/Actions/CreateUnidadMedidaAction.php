<?php

namespace App\Modules\Inventario\UnidadMedida\Actions;

use Illuminate\Support\Facades\DB;
use App\Models\UnidadMedida;
use App\Modules\Inventario\UnidadMedida\DTOs\CreateUnidadMedidaDTO;

class CreateUnidadMedidaAction
{
    public function __invoke(
        CreateUnidadMedidaDTO $dto
    ): UnidadMedida {

        return DB::transaction(
            function () use ($dto) {

                $model = UnidadMedida::create(
                    $dto->toArray()
                );

                /*
                |--------------------------------------------------------------------------
                | Future hooks
                |--------------------------------------------------------------------------
                */

                // app(UnidadMedidaService::class)
                //     ->afterCreate($model);

                return $model;
            }
        );
    }
}