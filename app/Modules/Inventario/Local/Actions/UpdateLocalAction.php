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

                $data = $dto->attributes();

                /*
                |--------------------------------------------------------------------------
                | Regla de negocio
                |--------------------------------------------------------------------------
                | Solo puede existir una local principal.
                |
                | Si esta local pasa a ser principal, todas las demás
                | locales dejan de ser principales.
                |--------------------------------------------------------------------------
                */
                if ((bool) ($data['es_principal'] ?? false)) {

                    Local::query()
                        ->where('id', '!=', $model->id)
                        ->where('es_principal', true)
                        ->update([
                            'es_principal' => false,
                        ]);
                }

                $model->update($data);

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
