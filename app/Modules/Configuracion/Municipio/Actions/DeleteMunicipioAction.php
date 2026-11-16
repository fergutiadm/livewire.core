<?php

namespace App\Modules\Configuracion\Municipio\Actions;

use Illuminate\Support\Facades\DB;
use App\Models\Municipio;
use App\Modules\Configuracion\Municipio\DTOs\DeleteMunicipioDTO;

class DeleteMunicipioAction
{
    public function __invoke(
        DeleteMunicipioDTO $dto
    ): bool {

        return DB::transaction(
            function () use ($dto) {

                $model = Municipio::findOrFail(
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

                // app(MunicipioService::class)
                //     ->beforeDelete($model);

                return (bool) $model->delete();
            }
        );
    }
}