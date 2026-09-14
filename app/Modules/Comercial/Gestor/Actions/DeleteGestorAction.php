<?php

namespace App\Modules\Comercial\Gestor\Actions;

use App\Models\Gestor;
use App\Modules\Comercial\Gestor\DTOs\DeleteGestorDTO;
use Illuminate\Support\Facades\DB;

class DeleteGestorAction
{
    public function __invoke(
        DeleteGestorDTO $dto
    ): bool {
        return DB::transaction(
            function () use ($dto) {

                $model = Gestor::findOrFail(
                    $dto->id
                );

                $model->update([
                    'activo' => false,
                ]);

                return (bool) $model->delete();
            }
        );
    }
}