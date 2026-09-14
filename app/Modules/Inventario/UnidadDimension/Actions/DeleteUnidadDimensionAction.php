<?php

namespace App\Modules\Inventario\UnidadDimension\Actions;

use Illuminate\Support\Facades\DB;
use App\Models\UnidadDimension;
use App\Modules\Inventario\UnidadDimension\DTOs\DeleteUnidadDimensionDTO;

class DeleteUnidadDimensionAction
{
    public function __invoke(
        DeleteUnidadDimensionDTO $dto
    ): bool {

        return DB::transaction(
            function () use ($dto) {

                $model = UnidadDimension::findOrFail(
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

                // app(UnidadDimensionService::class)
                //     ->beforeDelete($model);

                return (bool) $model->delete();
            }
        );
    }
}