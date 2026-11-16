<?php

namespace App\Modules\Inventario\TipoLocal\Actions;

use Illuminate\Support\Facades\DB;
use App\Models\TipoLocal;
use App\Modules\Inventario\TipoLocal\DTOs\UpdateTipoLocalDTO;

class UpdateTipoLocalAction
{
    public function __invoke(UpdateTipoLocalDTO $dto): TipoLocal {

        return DB::transaction(
            function () use ($dto) {

                $model = TipoLocal::findOrFail($dto->id);

                $model->update(
                    $dto->attributes()
                );

                $model->refresh();

                /*
                |--------------------------------------------------------------------------
                | Future hooks
                |--------------------------------------------------------------------------
                */

                // app(TipoLocalService::class)
                //     ->afterUpdate($model);

                return $model;
            }
        );
    }
}
