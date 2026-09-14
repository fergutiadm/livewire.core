<?php

namespace App\Modules\Inventario\ProductoUnidad\Actions;

use Illuminate\Support\Facades\DB;
use App\Models\ProductoUnidad;
use App\Modules\Inventario\ProductoUnidad\DTOs\DeleteProductoUnidadDTO;

class DeleteProductoUnidadAction
{
    public function __invoke(
        DeleteProductoUnidadDTO $dto
    ): bool {

        return DB::transaction(
            function () use ($dto) {

                $model = ProductoUnidad::findOrFail(
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

                // app(ProductoUnidadService::class)
                //     ->beforeDelete($model);

                return (bool) $model->delete();
            }
        );
    }
}