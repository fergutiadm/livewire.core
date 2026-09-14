<?php

namespace App\Modules\Contabilidad\PeriodoContable\Actions;

use Illuminate\Support\Facades\DB;
use App\Models\PeriodoContable;
use App\Modules\Contabilidad\PeriodoContable\DTOs\DeletePeriodoContableDTO;

class DeletePeriodoContableAction
{
    public function __invoke(
        DeletePeriodoContableDTO $dto
    ): bool {

        return DB::transaction(
            function () use ($dto) {

                $model = PeriodoContable::findOrFail(
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

                // app(PeriodoContableService::class)
                //     ->beforeDelete($model);

                return (bool) $model->delete();
            }
        );
    }
}