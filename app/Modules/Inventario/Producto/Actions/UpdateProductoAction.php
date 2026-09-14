<?php

namespace App\Modules\Inventario\Producto\Actions;

use Illuminate\Support\Facades\DB;
use App\Models\Producto;
use App\Modules\Inventario\Producto\DTOs\UpdateProductoDTO;

class UpdateProductoAction
{
    public function __invoke(UpdateProductoDTO $dto): Producto {

        return DB::transaction(
            function () use ($dto) {

                $model = Producto::findOrFail($dto->id);

                $model->update(
                    $dto->attributes()
                );

                $model->refresh();

                /*
                |--------------------------------------------------------------------------
                | Future hooks
                |--------------------------------------------------------------------------
                */

                // app(ProductoService::class)
                //     ->afterUpdate($model);

                return $model;
            }
        );
    }
}
