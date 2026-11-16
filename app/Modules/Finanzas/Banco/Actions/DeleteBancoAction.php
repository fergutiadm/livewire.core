<?php

namespace App\Modules\Finanzas\Banco\Actions;

use Illuminate\Support\Facades\DB;
use App\Models\Banco;
use App\Modules\Finanzas\Banco\DTOs\DeleteBancoDTO;

class DeleteBancoAction
{
    public function __invoke(
        DeleteBancoDTO $dto
    ): bool {

        return DB::transaction(
            function () use ($dto) {

                $model = Banco::findOrFail(
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

                // app(BancoService::class)
                //     ->beforeDelete($model);

                return (bool) $model->delete();
            }
        );
    }
}