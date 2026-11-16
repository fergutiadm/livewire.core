<?php

namespace App\Modules\Inventario\UnidadMedida\Actions;

use App\Models\UnidadMedida;

class ToggleUnidadMedidaEsBaseDimensionAction
{
    public function execute(UnidadMedida $unidadMedida): UnidadMedida
    {
        $unidadMedida->es_base_dimension = ! $unidadMedida->es_base_dimension;
        $unidadMedida->save();

        return $unidadMedida->fresh();
    }
}
