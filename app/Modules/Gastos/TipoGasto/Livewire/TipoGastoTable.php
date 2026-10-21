<?php

namespace App\Modules\Gastos\TipoGasto\Livewire;

use App\Core\Tables\TableComponent;
use App\Modules\Gastos\TipoGasto\Actions\ToggleTipoGastoFijoAction;
use App\Modules\Gastos\TipoGasto\Actions\ToggleTipoGastoActivoAction;
use App\Models\TipoGasto;
use Livewire\Attributes\Reactive;

class TipoGastoTable extends TableComponent
{
    #[Reactive]
    public ?int $localId = null;

    protected function tableClass(): string
    {
        return \App\Modules\Gastos\TipoGasto\Tables\TipoGastoTableDefinition::class;
    }

    protected function tableParameters(): array
    {
        return [
            'localId' => $this->localId,
        ];
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