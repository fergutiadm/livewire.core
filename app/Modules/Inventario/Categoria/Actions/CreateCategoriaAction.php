<?php

namespace App\Modules\Inventario\Categoria\Actions;

use App\Models\Categoria;
use App\Modules\Inventario\Categoria\DTOs\CreateCategoriaDTO;
use Illuminate\Support\Facades\DB;

class CreateCategoriaAction
{
    public function __invoke(
        CreateCategoriaDTO $dto
    ): Categoria {
        return DB::transaction(
            function () use ($dto) {

                $data = $dto->toArray();

                $ultimoOrden = Categoria::where(
                    'local_id',
                    $dto->local_id
                )->max('orden_visual');

                $data['orden_visual'] = ($ultimoOrden ?? 0) + 1;

                $model = Categoria::create($data);

                /*
                |--------------------------------------------------------------------------
                | Future hooks
                |--------------------------------------------------------------------------
                |
                | Aquí podrán incorporarse posteriormente eventos,
                | servicios o procesos posteriores a la creación.
                |
                */

                return $model;
            }
        );
    }
}
