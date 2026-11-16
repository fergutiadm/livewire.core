<?php

namespace App\Modules\Finanzas\Banco\Actions;

use Illuminate\Support\Facades\DB;
use App\Models\Banco;
use App\Modules\Finanzas\Banco\DTOs\CreateBancoDTO;

class CreateBancoAction
{
    public function __invoke(
        CreateBancoDTO $dto
    ): Banco {

        return DB::transaction(
            function () use ($dto) {

                $model = Banco::create(
                    $dto->toArray()
                );

                /*
                |--------------------------------------------------------------------------
                | Future hooks
                |--------------------------------------------------------------------------
                */

                // app(BancoService::class)
                //     ->afterCreate($model);

                return $model;
            }
        );
    }
}