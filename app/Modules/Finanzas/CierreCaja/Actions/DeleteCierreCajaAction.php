<?php

namespace App\Modules\Finanzas\CierreCaja\Actions;

use Illuminate\Support\Facades\DB;
use App\Models\CierreCaja;
use App\Modules\Finanzas\CierreCaja\DTOs\DeleteCierreCajaDTO;

class DeleteCierreCajaAction
{
    public function __invoke(
        DeleteCierreCajaDTO $dto
    ): bool {

        return DB::transaction(
            function () use ($dto) {

                $model = CierreCaja::findOrFail(
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

                // app(CierreCajaService::class)
                //     ->beforeDelete($model);

                return (bool) $model->delete();
            }
        );
    }
}