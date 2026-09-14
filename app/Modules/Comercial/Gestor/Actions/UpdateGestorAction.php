<?php

namespace App\Modules\Comercial\Gestor\Actions;

use App\Models\Gestor;
use App\Modules\Comercial\Gestor\DTOs\UpdateGestorDTO;
use Illuminate\Support\Facades\DB;
use LogicException;

class UpdateGestorAction
{
    public function __invoke(UpdateGestorDTO $dto): Gestor
    {
        return DB::transaction(function () use ($dto) {

            $gestor = Gestor::find($dto->id);

            if (!$gestor) {
                throw new LogicException(
                    'El gestor seleccionado no existe.'
                );
            }

            $gestor->update(
                $dto->attributes()
            );

            return $gestor->refresh();
        });
    }
}