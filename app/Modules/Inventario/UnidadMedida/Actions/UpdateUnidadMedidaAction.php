<?php

namespace App\Modules\Inventario\UnidadMedida\Actions;

use Illuminate\Support\Facades\DB;
use App\Models\UnidadMedida;
use App\Modules\Inventario\UnidadMedida\DTOs\UpdateUnidadMedidaDTO;

class UpdateUnidadMedidaAction
{
    public function __invoke(UpdateUnidadMedidaDTO $dto): UnidadMedida {

        return DB::transaction(
            function () use ($dto) {

                $model = UnidadMedida::findOrFail($dto->id);

                $model->update(
                    $dto->attributes()
                );

                $model->refresh();

                /*
                |--------------------------------------------------------------------------
                | Future hooks
                |--------------------------------------------------------------------------
                */

                // app(UnidadMedidaService::class)
                //     ->afterUpdate($model);

                return $model;
            }
        );
    }
}
