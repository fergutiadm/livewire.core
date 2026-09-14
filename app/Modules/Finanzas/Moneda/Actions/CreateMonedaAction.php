<?php

namespace App\Modules\Finanzas\Moneda\Actions;

use Illuminate\Support\Facades\DB;

use App\Models\Moneda;

use App\Modules\Finanzas\Moneda\DTOs\CreateMonedaDTO;

class CreateMonedaAction
{
    public function __invoke(
        CreateMonedaDTO $dto
    ): Moneda {
        return DB::transaction(
            function () use ($dto) {

                $data = $dto->toArray();

                /*
                |--------------------------------------------------------------------------
                | Regla de negocio
                |--------------------------------------------------------------------------
                | Solo puede existir una moneda principal.
                |
                | Si la nueva moneda es principal, todas las demás
                | monedas dejan de ser principales.
                |--------------------------------------------------------------------------
                */
                if ((bool) ($data['es_principal'] ?? false)) {

                    Moneda::query()
                        ->where('es_principal', true)
                        ->update([
                            'es_principal' => false,
                        ]);
                }

                $model = Moneda::create($data);

                /*
                |--------------------------------------------------------------------------
                | Future hooks
                |--------------------------------------------------------------------------
                */

                // app(MonedaService::class)
                //     ->afterCreate($model);

                return $model;
            }
        );
    }
}
