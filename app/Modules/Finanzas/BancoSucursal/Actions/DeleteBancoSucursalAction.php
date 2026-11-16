<?php

namespace App\Modules\Finanzas\BancoSucursal\Actions;

use Illuminate\Support\Facades\DB;
use App\Models\BancoSucursal;
use App\Modules\Finanzas\BancoSucursal\DTOs\DeleteBancoSucursalDTO;

class DeleteBancoSucursalAction
{
    public function __invoke(
        DeleteBancoSucursalDTO $dto
    ): bool {

        return DB::transaction(
            function () use ($dto) {

                $model = BancoSucursal::findOrFail(
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

                // app(BancoSucursalService::class)
                //     ->beforeDelete($model);

                return (bool) $model->delete();
            }
        );
    }
}