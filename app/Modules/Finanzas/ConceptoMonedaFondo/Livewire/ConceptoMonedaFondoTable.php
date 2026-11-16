<?php

namespace App\Modules\Finanzas\ConceptoMonedaFondo\Livewire;

use App\Core\Tables\TableComponent;
use App\Models\ConceptoMonedaFondo;

use App\Modules\Finanzas\ConceptoMonedaFondo\Actions\ToggleConceptoMonedaFondoActivoAction;
use App\Modules\Finanzas\ConceptoMonedaFondo\Actions\ToggleConceptoMonedaFondoEntradaSalidaAction;

//use App\Modules\Finanzas\ConceptoMonedaFondo\Tables\ConceptoMonedaFondoTableDefinition;

class ConceptoMonedaFondoTable extends TableComponent
{
    public function toggleActivo(int $conceptoMonedaFondoId): void
    {
        $conceptoMonedaFondo = ConceptoMonedaFondo::findOrFail($conceptoMonedaFondoId);

        app(ToggleConceptoMonedaFondoActivoAction::class)->execute($conceptoMonedaFondo);
    }

    public function toggleEntradaSalida(int $conceptoMonedaFondoId): void
    {
        $conceptoMonedaFondo = ConceptoMonedaFondo::findOrFail($conceptoMonedaFondoId);

        app(ToggleConceptoMonedaFondoEntradaSalidaAction::class)->execute($conceptoMonedaFondo);
    }

    protected function tableClass(): string
    {
        return \App\Modules\finanzas\ConceptoMonedaFondo\Tables\ConceptoMonedaFondoTableDefinition::class;
    }
}