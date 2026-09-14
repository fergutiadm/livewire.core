<?php

namespace App\Modules\Inventario\Local\Actions;

use Illuminate\Support\Facades\DB;
use App\Models\Local;
use App\Modules\Inventario\Local\DTOs\DeleteLocalDTO;

class DeleteLocalAction
{
    public function __invoke(
        DeleteLocalDTO $dto
    ): bool {

        return DB::transaction(
            function () use ($dto) {

                $model = Local::findOrFail(
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

                // app(LocalService::class)
                //     ->beforeDelete($model);

                return (bool) $model->delete();
            }
        );
    }
}