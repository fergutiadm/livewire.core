<?php

namespace App\Modules\Finanzas\BancoSucursal\Actions;

use Illuminate\Support\Facades\DB;
use App\Models\BancoSucursal;
use App\Modules\Finanzas\BancoSucursal\DTOs\UpdateBancoSucursalDTO;

class UpdateBancoSucursalAction
{
    public function __invoke(UpdateBancoSucursalDTO $dto): BancoSucursal {

        return DB::transaction(
            function () use ($dto) {

                $model = BancoSucursal::findOrFail($dto->id);

                $model->update(
                    $dto->attributes()
                );

                $model->refresh();

                /*
                |--------------------------------------------------------------------------
                | Future hooks
                |--------------------------------------------------------------------------
                */

                // app(BancoSucursalService::class)
                //     ->afterUpdate($model);

                return $model;
            }
        );
    }
}
