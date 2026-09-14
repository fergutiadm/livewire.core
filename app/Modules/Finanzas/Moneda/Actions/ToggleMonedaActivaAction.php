<?php

namespace App\Modules\Finanzas\Moneda\Actions;

use App\Models\Moneda;

class ToggleMonedaActivaAction
{
    public function execute(Moneda $moneda): Moneda
    {
        $moneda->activa = ! $moneda->activa;
        $moneda->save();

        return $moneda->fresh();
    }
}
