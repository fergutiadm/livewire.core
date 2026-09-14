<?php

namespace App\Modules\Comercial\Cliente\Actions;

use Illuminate\Support\Facades\DB;
use App\Models\Cliente;
use App\Modules\Comercial\Cliente\DTOs\UpdateClienteDTO;

class UpdateClienteAction
{
    public function __invoke(UpdateClienteDTO $dto): Cliente {

        return DB::transaction(
            function () use ($dto) {

                $model = Cliente::findOrFail($dto->id);

                $model->update(
                    $dto->attributes()
                );

                $model->refresh();

                /*
                |--------------------------------------------------------------------------
                | Future hooks
                |--------------------------------------------------------------------------
                */

                // app(ClienteService::class)
                //     ->afterUpdate($model);

                return $model;
            }
        );
    }
}
