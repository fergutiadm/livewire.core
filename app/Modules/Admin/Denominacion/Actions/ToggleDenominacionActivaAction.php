<?php

namespace App\Modules\Admin\Denominacion\Actions;

use App\Models\Denominacion;

class ToggleDenominacionActivaAction
{
    public function execute(Denominacion $denominacion): Denominacion
    {
        $denominacion->activa = ! $denominacion->activa;
        $denominacion->save();

        return $denominacion->fresh();
    }
}
