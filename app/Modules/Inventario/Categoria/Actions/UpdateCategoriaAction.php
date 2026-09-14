<?php

namespace App\Modules\Inventario\Categoria\Actions;

use App\Models\Categoria;
use App\Modules\Inventario\Categoria\DTOs\UpdateCategoriaDTO;
use Illuminate\Support\Facades\DB;

class UpdateCategoriaAction
{
    public function __invoke(
        UpdateCategoriaDTO $dto
    ): Categoria {
        //Logger()->info('UpdateCategoriaAction-CLASS.::',['id'=>$dto->id, 'attributes'=>$dto->attributes()]);
        return DB::transaction(
            function () use ($dto) {

                $model = Categoria::findOrFail(
                    $dto->id
                );


                $model->update(
                    $dto->attributes()
                );

                $model->refresh();

                /*
                |--------------------------------------------------------------------------
                | Future hooks
                |--------------------------------------------------------------------------
                */

                return $model;
            }
        );
    }
}
