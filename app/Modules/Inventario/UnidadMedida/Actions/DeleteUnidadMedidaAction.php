<?php

namespace App\Modules\Inventario\UnidadMedida\Actions;

use Illuminate\Support\Facades\DB;
use App\Models\UnidadMedida;
use App\Modules\Inventario\UnidadMedida\DTOs\DeleteUnidadMedidaDTO;

class DeleteUnidadMedidaAction
{
    public function __invoke(
        DeleteUnidadMedidaDTO $dto
    ): bool {

        return DB::transaction(
            function () use ($dto) {

                $model = UnidadMedida::findOrFail(
                    $dto->id
                );

                /*
                |--------------------------------------------------------------------------
                | Future business rules
                |--------------------------------------------------------------------------
                */

                /*
                |--------------------------------------------------------------------------
                | Future hooks
                |--------------------------------------------------------------------------
                */

                // app(UnidadMedidaService::class)
                //     ->beforeDelete($model);

                return (bool) $model->delete();
            }
        );
    }
}