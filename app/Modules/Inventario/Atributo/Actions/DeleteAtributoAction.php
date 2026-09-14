<?php

namespace App\Modules\Inventario\Atributo\Actions;

use Illuminate\Support\Facades\DB;
use App\Models\Atributo;
use App\Modules\Inventario\Atributo\DTOs\DeleteAtributoDTO;

class DeleteAtributoAction
{
    public function __invoke(
        DeleteAtributoDTO $dto
    ): bool {

        return DB::transaction(
            function () use ($dto) {

                $model = Atributo::findOrFail(
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

                // app(AtributoService::class)
                //     ->beforeDelete($model);

                return (bool) $model->delete();
            }
        );
    }
}