<?php

namespace App\Modules\Inventario\TipoLocal\Actions;

use Illuminate\Support\Facades\DB;
use App\Models\TipoLocal;
use App\Modules\Inventario\TipoLocal\DTOs\CreateTipoLocalDTO;

class CreateTipoLocalAction
{
    public function __invoke(
        CreateTipoLocalDTO $dto
    ): TipoLocal {

        return DB::transaction(
            function () use ($dto) {

                $model = TipoLocal::create(
                    $dto->toArray()
                );

                /*
                |--------------------------------------------------------------------------
                | Future hooks
                |--------------------------------------------------------------------------
                */

                // app(TipoLocalService::class)
                //     ->afterCreate($model);

                return $model;
            }
        );
    }
}