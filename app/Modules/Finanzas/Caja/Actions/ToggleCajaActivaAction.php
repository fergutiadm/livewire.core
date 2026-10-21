<?php

namespace App\Modules\Finanzas\Caja\Actions;

use App\Models\Caja;

class ToggleCajaActivaAction
{
    public function execute(Caja $caja): Caja
    {
        $caja->activa = ! $caja->activa;
        $caja->save();

        return $caja->fresh();
    }
}
