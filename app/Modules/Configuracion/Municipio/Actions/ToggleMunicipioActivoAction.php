<?php

namespace App\Modules\Configuracion\Municipio\Actions;

use App\Models\Municipio;

class ToggleMunicipioActivoAction
{
    public function execute(Municipio $municipio): Municipio
    {
        $municipio->activo = ! $municipio->activo;
        $municipio->save();

        return $municipio->fresh();
    }
}
