<?php

namespace App\Modules\Inventario\ProductoUnidad\Actions;

use Illuminate\Support\Facades\DB;
use App\Models\ProductoUnidad;
use App\Modules\Inventario\ProductoUnidad\DTOs\UpdateProductoUnidadDTO;

class UpdateProductoUnidadAction
{
    public function __invoke(UpdateProductoUnidadDTO $dto): ProductoUnidad {

        return DB::transaction(
            function () use ($dto) {

                $model = ProductoUnidad::findOrFail($dto->id);

                $model->update(
                    $dto->attributes()
                );

                $model->refresh();

                /*
                |--------------------------------------------------------------------------
                | Future hooks
                |--------------------------------------------------------------------------
                */

                // app(ProductoUnidadService::class)
                //     ->afterUpdate($model);

                return $model;
            }
        );
    }
}
