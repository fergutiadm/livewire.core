<?php

namespace App\Modules\Ayuda\AyudaCategoria\Actions;

use App\Models\AyudaCategoria;
use App\Modules\Ayuda\AyudaCategoria\DTOs\DeleteAyudaCategoriaDTO;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DeleteAyudaCategoriaAction
{
    public function __invoke(
        DeleteAyudaCategoriaDTO $dto
    ): bool {

        return DB::transaction(function () use ($dto) {

            $model = AyudaCategoria::query()
                ->lockForUpdate()
                ->findOrFail($dto->id);

            if ($model->articulos()->exists()) {
                throw ValidationException::withMessages([
                    'categoria' =>
                        'No se puede eliminar la categoría porque tiene artículos asociados.',
                ]);
            }

            return (bool) $model->delete();
        });
    }
}
