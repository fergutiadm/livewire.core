<?php

namespace App\Modules\Inventario\Local\Actions;

use Illuminate\Support\Facades\DB;
use App\Models\Local;
use App\Modules\Inventario\Local\DTOs\CreateLocalDTO;

class CreateLocalAction
{
    public function __invoke(
        CreateLocalDTO $dto
    ): Local {

        return DB::transaction(
            function () use ($dto) {

                $model = Local::create(
                    $dto->toArray()
                );

                /*
                |--------------------------------------------------------------------------
                | Future hooks
                |--------------------------------------------------------------------------
                */

                // app(LocalService::class)
                //     ->afterCreate($model);

                return $model;
            }
        );
    }
}