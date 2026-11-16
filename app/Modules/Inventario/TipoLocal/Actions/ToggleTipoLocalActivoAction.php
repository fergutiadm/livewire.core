<?php

namespace App\Modules\Inventario\TipoLocal\Actions;

use App\Models\TipoLocal;

class ToggleTipoLocalActivoAction
{
    public function execute(TipoLocal $tipoLocal): TipoLocal
    {
        $tipoLocal->activo = ! $tipoLocal->activo;
        $tipoLocal->save();

        return $tipoLocal->fresh();
    }
}
