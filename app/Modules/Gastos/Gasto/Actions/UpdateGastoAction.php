<?php

namespace App\Modules\Gastos\Gasto\Actions;

use App\Models\Gasto;
use App\Modules\Gastos\Gasto\DTOs\UpdateGastoDTO;
use Illuminate\Support\Facades\DB;

class UpdateGastoAction
{
    public function __invoke(UpdateGastoDTO $dto): Gasto
    {
        return DB::transaction(function () use ($dto) {

            $model = Gasto::findOrFail($dto->id);

            $model->update($dto->attributes());

            return $model->fresh();
        });
    }
}