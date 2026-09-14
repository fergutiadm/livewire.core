<?php

namespace App\Modules\Finanzas\TarjetaMagnetica\Actions;

use Illuminate\Support\Facades\DB;
use App\Models\TarjetaMagnetica;
use App\Modules\Finanzas\TarjetaMagnetica\DTOs\DeleteTarjetaMagneticaDTO;

class DeleteTarjetaMagneticaAction
{
    public function __invoke(
        DeleteTarjetaMagneticaDTO $dto
    ): bool {

        return DB::transaction(
            function () use ($dto) {

                $model = TarjetaMagnetica::findOrFail(
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

                // app(TarjetaMagneticaService::class)
                //     ->beforeDelete($model);

                return (bool) $model->delete();
            }
        );
    }
}