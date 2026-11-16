<?php

namespace App\Modules\Finanzas\CuentaBancaria\Actions;

use Illuminate\Support\Facades\DB;
use App\Models\CuentaBancaria;
use App\Modules\Finanzas\CuentaBancaria\DTOs\DeleteCuentaBancariaDTO;

class DeleteCuentaBancariaAction
{
    public function __invoke(
        DeleteCuentaBancariaDTO $dto
    ): bool {

        return DB::transaction(
            function () use ($dto) {

                $model = CuentaBancaria::findOrFail(
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

                // app(CuentaBancariaService::class)
                //     ->beforeDelete($model);

                return (bool) $model->delete();
            }
        );
    }
}