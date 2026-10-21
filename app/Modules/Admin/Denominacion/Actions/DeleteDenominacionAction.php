<?php

namespace App\Modules\Admin\Denominacion\Actions;

use Illuminate\Support\Facades\DB;
use App\Models\Denominacion;
use App\Modules\Admin\Denominacion\DTOs\DeleteDenominacionDTO;

class DeleteDenominacionAction
{
    public function __invoke(
        DeleteDenominacionDTO $dto
    ): bool {

        return DB::transaction(
            function () use ($dto) {

                $model = Denominacion::findOrFail(
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

                // app(DenominacionService::class)
                //     ->beforeDelete($model);

                return (bool) $model->delete();
            }
        );
    }
}