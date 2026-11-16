<?php

namespace App\Modules\Finanzas\Banco\Actions;

use Illuminate\Support\Facades\DB;
use App\Models\Banco;
use App\Modules\Finanzas\Banco\DTOs\UpdateBancoDTO;

class UpdateBancoAction
{
    public function __invoke(UpdateBancoDTO $dto): Banco {

        return DB::transaction(
            function () use ($dto) {

                $model = Banco::findOrFail($dto->id);

                $model->update(
                    $dto->attributes()
                );

                $model->refresh();

                /*
                |--------------------------------------------------------------------------
                | Future hooks
                |--------------------------------------------------------------------------
                */

                // app(BancoService::class)
                //     ->afterUpdate($model);

                return $model;
            }
        );
    }
}
