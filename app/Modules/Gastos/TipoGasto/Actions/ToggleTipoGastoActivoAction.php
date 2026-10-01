<?php

namespace App\Modules\Finanzas\TipoGasto\Actions;

use App\Models\TipoGasto;

class ToggleTipoGastoActivoAction
{
    public function execute(TipoGasto $tipoGasto): TipoGasto
    {
        $tipoGasto->activo = ! $tipoGasto->activo;
        $tipoGasto->save();

        return $tipoGasto->fresh();
    }
}
