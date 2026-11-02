<?php

namespace App\Modules\Ayuda\AyudaArticulo\Actions;

use App\Models\AyudaArticulo;
use App\Modules\Ayuda\AyudaArticulo\DTOs\DeleteAyudaArticuloDTO;
use Illuminate\Support\Facades\DB;

class DeleteAyudaArticuloAction
{
    public function __invoke(
        DeleteAyudaArticuloDTO $dto
    ): bool {

        return DB::transaction(function () use ($dto) {

            $model = AyudaArticulo::query()
                ->lockForUpdate()
                ->findOrFail($dto->id);

            return (bool) $model->delete();
        });
    }
}
