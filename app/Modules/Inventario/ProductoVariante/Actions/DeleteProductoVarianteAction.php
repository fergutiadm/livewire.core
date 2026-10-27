<?php

namespace App\Modules\Inventario\ProductoVariante\Actions;

use Illuminate\Support\Facades\DB;
use App\Models\ProductoVariante;
use App\Modules\Inventario\ProductoVariante\DTOs\DeleteProductoVarianteDTO;

class DeleteProductoVarianteAction
{
    public function __invoke(
        DeleteProductoVarianteDTO $dto
    ): bool {

        return DB::transaction(
            function () use ($dto) {

                $model = ProductoVariante::findOrFail(
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

                // app(ProductoVarianteService::class)
                //     ->beforeDelete($model);

                return (bool) $model->delete();
            }
        );
    }
}