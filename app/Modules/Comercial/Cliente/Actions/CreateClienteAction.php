<?php

namespace App\Modules\Comercial\Cliente\Actions;

use Illuminate\Support\Facades\DB;
use App\Models\Cliente;
use App\Modules\Comercial\Cliente\DTOs\CreateClienteDTO;

class CreateClienteAction
{
    public function __invoke(
        CreateClienteDTO $dto
    ): Cliente {

        return DB::transaction(
            function () use ($dto) {

                $model = Cliente::create(
                    $dto->toArray()
                );

                /*
                |--------------------------------------------------------------------------
                | Future hooks
                |--------------------------------------------------------------------------
                */

                // app(ClienteService::class)
                //     ->afterCreate($model);

                return $model;
            }
        );
    }
}