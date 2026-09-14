<?php

namespace App\Modules\Inventario\ProductoUnidad\Actions;

use Illuminate\Support\Facades\DB;
use App\Models\ProductoUnidad;
use App\Modules\Inventario\ProductoUnidad\DTOs\CreateProductoUnidadDTO;

class CreateProductoUnidadAction
{
    public function __invoke(
        CreateProductoUnidadDTO $dto
    ): ProductoUnidad {

        return DB::transaction(
            function () use ($dto) {

                $model = ProductoUnidad::create(
                    $dto->toArray()
                );

                /*
                |--------------------------------------------------------------------------
                | Future hooks
                |--------------------------------------------------------------------------
                */

                // app(ProductoUnidadService::class)
                //     ->afterCreate($model);

                return $model;
            }
        );
    }
}