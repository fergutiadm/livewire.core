<?php

namespace App\Modules\Finanzas\CuentaBancaria\Actions;

use Illuminate\Support\Facades\DB;
use App\Models\CuentaBancaria;
use App\Modules\Finanzas\CuentaBancaria\DTOs\UpdateCuentaBancariaDTO;

class UpdateCuentaBancariaAction
{
    public function __invoke(UpdateCuentaBancariaDTO $dto): CuentaBancaria {

        return DB::transaction(
            function () use ($dto) {

                $model = CuentaBancaria::findOrFail($dto->id);

                $model->update(
                    $dto->attributes()
                );

                $model->refresh();

                /*
                |--------------------------------------------------------------------------
                | Future hooks
                |--------------------------------------------------------------------------
                */

                // app(CuentaBancariaService::class)
                //     ->afterUpdate($model);

                return $model;
            }
        );
    }
}
