<?php

namespace App\Modules\Finanzas\CuentaBancaria\Actions;

use Illuminate\Support\Facades\DB;
use App\Models\CuentaBancaria;
use App\Modules\Finanzas\CuentaBancaria\DTOs\CreateCuentaBancariaDTO;

class CreateCuentaBancariaAction
{
    public function __invoke(
        CreateCuentaBancariaDTO $dto
    ): CuentaBancaria {

        return DB::transaction(
            function () use ($dto) {

                $model = CuentaBancaria::create(
                    $dto->toArray()
                );

                /*
                |--------------------------------------------------------------------------
                | Future hooks
                |--------------------------------------------------------------------------
                */

                // app(CuentaBancariaService::class)
                //     ->afterCreate($model);

                return $model;
            }
        );
    }
}