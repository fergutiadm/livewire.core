<?php

namespace App\Modules\Comercial\Cliente\Actions;

use Illuminate\Support\Facades\DB;
use App\Models\Cliente;
use App\Modules\Comercial\Cliente\DTOs\DeleteClienteDTO;

class DeleteClienteAction
{
    public function __invoke(
        DeleteClienteDTO $dto
    ): bool {

        return DB::transaction(
            function () use ($dto) {

                $model = Cliente::findOrFail(
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

                // app(ClienteService::class)
                //     ->beforeDelete($model);

                return (bool) $model->delete();
            }
        );
    }
}