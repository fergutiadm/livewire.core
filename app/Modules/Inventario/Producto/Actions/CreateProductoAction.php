<?php

namespace App\Modules\Inventario\Producto\Actions;

use Illuminate\Support\Facades\DB;
use App\Models\Producto;
use App\Modules\Inventario\Producto\DTOs\CreateProductoDTO;

class CreateProductoAction
{
    public function __invoke(CreateProductoDTO $dto): Producto {

        return DB::transaction(
            function () use ($dto) {

                $model = Producto::create(
                    $dto->toArray()
                );

                /*
                |--------------------------------------------------------------------------
                | Future hooks
                |--------------------------------------------------------------------------
                */

                // app(ProductoService::class)
                //     ->afterCreate($model);

                return $model;
            }
        );
    }
}