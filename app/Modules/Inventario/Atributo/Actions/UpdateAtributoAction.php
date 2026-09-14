<?php

namespace App\Modules\Inventario\Atributo\Actions;

use Illuminate\Support\Facades\DB;
use App\Models\Atributo;
use App\Modules\Inventario\Atributo\DTOs\UpdateAtributoDTO;

class UpdateAtributoAction
{
    public function __invoke(UpdateAtributoDTO $dto): Atributo {

        return DB::transaction(
            function () use ($dto) {

                $model = Atributo::findOrFail($dto->id);

                $model->update(
                    $dto->attributes()
                );

                $model->refresh();

                /*
                |--------------------------------------------------------------------------
                | Future hooks
                |--------------------------------------------------------------------------
                */

                // app(AtributoService::class)
                //     ->afterUpdate($model);

                return $model;
            }
        );
    }
}
