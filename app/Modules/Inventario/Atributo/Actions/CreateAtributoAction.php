<?php

namespace App\Modules\Inventario\Atributo\Actions;

use Illuminate\Support\Facades\DB;
use App\Models\Atributo;
use App\Modules\Inventario\Atributo\DTOs\CreateAtributoDTO;

class CreateAtributoAction
{
    public function __invoke(
        CreateAtributoDTO $dto
    ): Atributo {

        return DB::transaction(
            function () use ($dto) {

                $data = $dto->toArray();

                $ultimoOrden = Atributo::max('orden_visual');

                $data['orden_visual'] = ($ultimoOrden ?? 0) + 1;

                $model = Atributo::create($data);

                /*
                |--------------------------------------------------------------------------
                | Future hooks
                |--------------------------------------------------------------------------
                */

                // app(AtributoService::class)
                //     ->afterCreate($model);

                return $model;
            }
        );
    }
}