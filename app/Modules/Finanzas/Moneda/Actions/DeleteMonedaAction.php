<?php

namespace App\Modules\Finanzas\Moneda\Actions;

use Illuminate\Support\Facades\DB;
use App\Models\Moneda;
use App\Modules\Finanzas\Moneda\DTOs\DeleteMonedaDTO;
use App\Services\MonedaService;

class DeleteMonedaAction
{
    public function __invoke(
        DeleteMonedaDTO $dto
    ): bool {

        return DB::transaction(
            function () use ($dto) {

                $model = Moneda::findOrFail(
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

                // app(MonedaService::class)
                //     ->beforeDelete($model);

                $result = (bool) $model->delete();
                app(MonedaService::class)->forgetCache();
                return $result;
            }
        );
    }
}