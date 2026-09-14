<?php

namespace App\Modules\Inventario\UnidadDimension\Actions;

use Illuminate\Support\Facades\DB;
use App\Models\UnidadDimension;
use App\Modules\Inventario\UnidadDimension\DTOs\CreateUnidadDimensionDTO;

class CreateUnidadDimensionAction
{
    public function __invoke(
        CreateUnidadDimensionDTO $dto
    ): UnidadDimension {

        return DB::transaction(
            function () use ($dto) {

                $model = UnidadDimension::create(
                    $dto->toArray()
                );

                /*
                |--------------------------------------------------------------------------
                | Future hooks
                |--------------------------------------------------------------------------
                */

                // app(UnidadDimensionService::class)
                //     ->afterCreate($model);

                return $model;
            }
        );
    }
}