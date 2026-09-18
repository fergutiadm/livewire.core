<?php

namespace App\Modules\Inventario\Atributo\Actions;

use App\Models\Atributo;
use App\Modules\Inventario\Atributo\DTOs\DeleteAtributoDTO;
use Illuminate\Support\Facades\DB;

class DeleteAtributoAction
{
    public function execute(DeleteAtributoDTO $dto): void
    {
        DB::transaction(function () use ($dto) {
            $atributo = Atributo::query()
                ->lockForUpdate()
                ->findOrFail($dto->id);

            $atributo->update([
                'activo' => false,
            ]);

            $atributo->valores()->update([
                'activo' => false,
            ]);
        });
    }
}