<?php

namespace App\Modules\Inventario\Local\Actions;

use Illuminate\Support\Facades\DB;
use App\Models\Local;
use App\Modules\Inventario\Local\DTOs\UpdateLocalDTO;

class UpdateLocalAction
{
    public function __invoke(UpdateLocalDTO $dto): Local {

        return DB::transaction(
            function () use ($dto) {

                $model = Local::findOrFail($dto->id);

                $model->update(
                    $dto->attributes()
                );

                $model->refresh();

                /*
                |--------------------------------------------------------------------------
                | Future hooks
                |--------------------------------------------------------------------------
                */

                // app(LocalService::class)
                //     ->afterUpdate($model);

                return $model;
            }
        );
    }
}
