<?php

namespace App\Modules\Configuracion\Provincia\Actions;

use App\Models\Provincia;

class ToggleProvinciaActivaAction
{
    public function execute(Provincia $provincia): Provincia
    {
        $provincia->activa = ! $provincia->activa;
        $provincia->save();

        return $provincia->fresh();
    }
}
