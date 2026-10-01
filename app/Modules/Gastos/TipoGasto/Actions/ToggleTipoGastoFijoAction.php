<?php

namespace App\Modules\Finanzas\TipoGasto\Actions;

use App\Models\TipoGasto;

class ToggleTipoGastoFijoAction
{
    public function execute(TipoGasto $tipoGasto): TipoGasto
    {
        $tipoGasto->fijo = ! $tipoGasto->fijo;
        $tipoGasto->save();

        return $tipoGasto->fresh();
    }
}
