<?php

namespace App\Modules\Gastos\TipoGasto\Livewire;

use App\Core\Tables\TableComponent;
//use App\Modules\Gastos\TipoGasto\Tables\TipoGastoTableDefinition;

class TipoGastoTable extends TableComponent
{
    protected function tableClass(): string
    {
        return \App\Modules\gastos\TipoGasto\Tables\TipoGastoTableDefinition::class;
    }

    public function toggleFijo(int $tipoGastoId): void
    {
        $tipoGasto = TipoGasto::findOrFail($tipoGastoId);

        app(ToggleTipoGastoFijoAction::class)->execute($tipoGasto);
    }

    public function toggleActivo(int $tipoGastoId): void
    {
        $tipoGasto = TipoGasto::findOrFail($tipoGastoId);

        app(ToggleTipoGastoActivoAction::class)->execute($tipoGasto);
    }

    public function placeholder()
    {
        return view('components.loading-placeholder', [
            'message' => 'Cargando tipos de gastos...',
        ]);
    }
}