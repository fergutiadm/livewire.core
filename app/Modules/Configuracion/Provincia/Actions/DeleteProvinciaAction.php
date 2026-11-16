<?php

namespace App\Modules\Configuracion\Provincia\Actions;

use Illuminate\Support\Facades\DB;
use App\Models\Provincia;
use App\Modules\Configuracion\Provincia\DTOs\DeleteProvinciaDTO;

class DeleteProvinciaAction
{
    public function __invoke(
        DeleteProvinciaDTO $dto
    ): bool {

        return DB::transaction(
            function () use ($dto) {

                $model = Provincia::findOrFail(
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

                // app(ProvinciaService::class)
                //     ->beforeDelete($model);

                return (bool) $model->delete();
            }
        );
    }
}