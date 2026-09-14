<?php

namespace App\Modules\Contabilidad\PeriodoContable\Actions;

use Illuminate\Support\Facades\DB;
use App\Models\PeriodoContable;
use App\Modules\Contabilidad\PeriodoContable\DTOs\UpdatePeriodoContableDTO;

class UpdatePeriodoContableAction
{
    public function __invoke(UpdatePeriodoContableDTO $dto): PeriodoContable {

        return DB::transaction(
            function () use ($dto) {

                $model = PeriodoContable::findOrFail($dto->id);

                $model->update(
                    $dto->attributes()
                );

                $model->refresh();

                /*
                |--------------------------------------------------------------------------
                | Future hooks
                |--------------------------------------------------------------------------
                */

                // app(PeriodoContableService::class)
                //     ->afterUpdate($model);

                return $model;
            }
        );
    }
}
