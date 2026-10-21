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

                $data = $dto->toArray();

                /*
                |--------------------------------------------------------------------------
                | Regla de negocio
                |--------------------------------------------------------------------------
                | Solo puede existir una local principal.
                |
                | Si la nueva local es principal, todas las demás
                | locales dejan de ser principales.
                |--------------------------------------------------------------------------
                */
                if ((bool) ($data['es_principal'] ?? false)) {

                    Local::query()
                        ->where('es_principal', true)
                        ->update([
                            'es_principal' => false,
                        ]);
                }

                $model = Local::create($data);

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