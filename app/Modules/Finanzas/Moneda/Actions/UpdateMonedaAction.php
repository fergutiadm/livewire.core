<?php

namespace App\Modules\Finanzas\Moneda\Actions;

use Illuminate\Support\Facades\DB;

use App\Models\Moneda;

use App\Modules\Finanzas\Moneda\DTOs\UpdateMonedaDTO;

class UpdateMonedaAction
{
    public function __invoke(
        UpdateMonedaDTO $dto
    ): Moneda {
        return DB::transaction(
            function () use ($dto) {

                $model = Moneda::findOrFail($dto->id);

                $data = $dto->attributes();

                /*
                |--------------------------------------------------------------------------
                | Regla de negocio
                |--------------------------------------------------------------------------
                | Solo puede existir una moneda principal.
                |
                | Si esta moneda pasa a ser principal, todas las demás
                | monedas dejan de ser principales.
                |--------------------------------------------------------------------------
                */
                if ((bool) ($data['es_principal'] ?? false)) {

                    Moneda::query()
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

                // app(MonedaService::class)
                //     ->afterUpdate($model);

                return $model;
            }
        );
    }
}
