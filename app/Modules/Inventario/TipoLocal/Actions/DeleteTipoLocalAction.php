<?php

namespace App\Modules\Inventario\TipoLocal\Actions;

use Illuminate\Support\Facades\DB;
use App\Models\TipoLocal;
use App\Modules\Inventario\TipoLocal\DTOs\DeleteTipoLocalDTO;

class DeleteTipoLocalAction
{
    public function __invoke(
        DeleteTipoLocalDTO $dto
    ): bool {

        return DB::transaction(
            function () use ($dto) {

                $model = TipoLocal::findOrFail(
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

                // app(TipoLocalService::class)
                //     ->beforeDelete($model);

                return (bool) $model->delete();
            }
        );
    }
}