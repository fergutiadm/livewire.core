<?php

namespace App\Modules\Finanzas\BancoSucursal\Actions;

use Illuminate\Support\Facades\DB;
use App\Models\BancoSucursal;
use App\Modules\Finanzas\BancoSucursal\DTOs\CreateBancoSucursalDTO;

class CreateBancoSucursalAction
{
    public function __invoke(
        CreateBancoSucursalDTO $dto
    ): BancoSucursal {

        return DB::transaction(
            function () use ($dto) {

                $model = BancoSucursal::create(
                    $dto->toArray()
                );

                /*
                |--------------------------------------------------------------------------
                | Future hooks
                |--------------------------------------------------------------------------
                */

                // app(BancoSucursalService::class)
                //     ->afterCreate($model);

                return $model;
            }
        );
    }
}