<?php

namespace App\Modules\Inventario\Producto\Actions;

use Illuminate\Support\Facades\DB;

use App\Models\Producto;

use App\Modules\Inventario\Producto\DTOs\DeleteProductoDTO;

class DeleteProductoAction
{
    public function __invoke(DeleteProductoDTO $dto): bool
    {
        return DB::transaction(
            function () use ($dto) {

                $model = Producto::findOrFail(
                    $dto->id
                );

                /*
                |--------------------------------------------------------------------------
                | Future business rules
                |--------------------------------------------------------------------------
                */

                // if ($model->hasRelations()) {
                //     throw new \Exception(
                //         'Cannot delete model with relations.'
                //     );
                // }

                /*
                |--------------------------------------------------------------------------
                | Future hooks
                |--------------------------------------------------------------------------
                */

                // app(ProductoService::class)
                //     ->beforeDelete($model);

                return (bool) $model->delete();
            }
        );
    }
}