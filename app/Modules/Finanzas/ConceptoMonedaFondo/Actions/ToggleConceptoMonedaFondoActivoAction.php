<?php

namespace App\Modules\Finanzas\ConceptoMonedaFondo\Actions;

use App\Models\ConceptoMonedaFondo;

class ToggleConceptoMonedaFondoActivoAction
{
    public function execute(ConceptoMonedaFondo $conceptoMonedaFondo): ConceptoMonedaFondo
    {
        $conceptoMonedaFondo->activo = ! $conceptoMonedaFondo->activo;
        $conceptoMonedaFondo->save();

        return $conceptoMonedaFondo->fresh();
    }
}
