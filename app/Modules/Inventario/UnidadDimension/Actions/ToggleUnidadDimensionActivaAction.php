<?php

namespace App\Modules\Inventario\UnidadDimension\Actions;

use App\Models\UnidadDimension;

class ToggleUnidadDimensionActivaAction
{
    public function execute(UnidadDimension $unidadDimension): UnidadDimension
    {
        $unidadDimension->activa = ! $unidadDimension->activa;
        $unidadDimension->save();

        return $unidadDimension->fresh();
    }
}
