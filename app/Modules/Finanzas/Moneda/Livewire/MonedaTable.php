<?php

namespace App\Modules\Finanzas\Moneda\Livewire;

use App\Core\Tables\TableComponent;

use App\Models\Moneda;
use App\Modules\Finanzas\Moneda\Actions\ToggleMonedaActivaAction;

class MonedaTable extends TableComponent
{
    protected function tableClass(): string
    {
        return \App\Modules\Finanzas\Moneda\Tables\MonedaTableDefinition::class;
    }

    public function toggleActiva(int $monedaId): void
    {
        $moneda = Moneda::findOrFail($monedaId);

        app(ToggleMonedaActivaAction::class)->execute($moneda);
    }

    public function placeholder()
    {
        return view('components.loading-placeholder', [
            'message' => 'Cargando monedas...',
        ]);
    }
}
