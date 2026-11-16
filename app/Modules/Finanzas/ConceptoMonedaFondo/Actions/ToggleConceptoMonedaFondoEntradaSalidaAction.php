<?php

namespace App\Modules\Finanzas\ConceptoMonedaFondo\Actions;

use App\Models\ConceptoMonedaFondo;

class ToggleConceptoMonedaFondoEntradaSalidaAction
{
    public function execute(ConceptoMonedaFondo $conceptoMonedaFondo): ConceptoMonedaFondo
    {
        $conceptoMonedaFondo->entrada_salida = ! $conceptoMonedaFondo->entrada_salida;
        $conceptoMonedaFondo->save();

        return $conceptoMonedaFondo->fresh();
    }
}
