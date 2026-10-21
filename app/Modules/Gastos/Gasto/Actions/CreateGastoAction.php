<?php

namespace App\Modules\Gastos\Gasto\Actions;

use App\Models\Gasto;
use App\Modules\Gastos\Gasto\DTOs\CreateGastoDTO;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CreateGastoAction
{
    public function __invoke(CreateGastoDTO $dto): Gasto
    {
        return DB::transaction(function () use ($dto) {

            $model = Gasto::create([
                ...$dto->toArray(),

                'operador_id' => Auth::id(),
                'chequeado' => false,
                'fecha_chequeado' => null,
                'ip_var' => request()->ip(),
            ]);

            return $model->fresh();
        });
    }
}