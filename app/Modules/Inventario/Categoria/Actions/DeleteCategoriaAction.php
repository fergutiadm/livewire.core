<?php

namespace App\Modules\Inventario\Categoria\Actions;

use Illuminate\Support\Facades\DB;
use App\Models\Categoria;
use App\Modules\Inventario\Categoria\DTOs\DeleteCategoriaDTO;

class DeleteCategoriaAction
{
    public function __invoke(
        DeleteCategoriaDTO $dto
    ): bool {

        return DB::transaction(
            function () use ($dto) {

                $model = Categoria::findOrFail(
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

                // app(CategoriaService::class)
                //     ->beforeDelete($model);

                return (bool) $model->delete();
            }
        );
    }
}