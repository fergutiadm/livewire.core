<?php

namespace App\Modules\Gastos\Gasto\Actions;

use App\Models\Gasto;
use App\Modules\Gastos\Gasto\DTOs\CheckGastoDTO;
use Illuminate\Support\Facades\DB;
use LogicException;

class ChequearGastoAction
{
    public function __invoke(CheckGastoDTO $dto): Gasto
    {
        return DB::transaction(function () use ($dto) {

            $model = Gasto::findOrFail($dto->id);

            if ($model->chequeado) {
                throw new LogicException(
                    "El gasto [{$model->id}] ya está chequeado."
                );
            }

            $model->update([
                'chequeado' => true,
                'fecha_chequeado' => now(),
            ]);

            return $model->fresh();
        });
    }
}