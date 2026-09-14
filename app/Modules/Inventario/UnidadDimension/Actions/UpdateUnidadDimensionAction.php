<?php

namespace App\Modules\Inventario\UnidadDimension\Actions;

use Illuminate\Support\Facades\DB;
use App\Models\UnidadDimension;
use App\Modules\Inventario\UnidadDimension\DTOs\UpdateUnidadDimensionDTO;

class UpdateUnidadDimensionAction
{
    public function __invoke(UpdateUnidadDimensionDTO $dto): UnidadDimension {

        return DB::transaction(
            function () use ($dto) {

                $model = UnidadDimension::findOrFail($dto->id);

                $model->update(
                    $dto->attributes()
                );

                $model->refresh();

                /*
                |--------------------------------------------------------------------------
                | Future hooks
                |--------------------------------------------------------------------------
                */

                // app(UnidadDimensionService::class)
                //     ->afterUpdate($model);

                return $model;
            }
        );
    }
}
