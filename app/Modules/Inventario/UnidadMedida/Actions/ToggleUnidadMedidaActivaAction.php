<?php

namespace App\Modules\Inventario\UnidadMedida\Actions;

use App\Models\UnidadMedida;

class ToggleUnidadMedidaActivaAction
{
    public function execute(UnidadMedida $unidadMedida): UnidadMedida
    {
        $unidadMedida->activa = ! $unidadMedida->activa;
        $unidadMedida->save();

        return $unidadMedida->fresh();
    }
}
