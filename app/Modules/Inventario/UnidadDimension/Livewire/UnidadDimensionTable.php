<?php

namespace App\Modules\Inventario\UnidadDimension\Livewire;

use App\Core\Tables\TableComponent;
use App\Models\UnidadDimension;
use App\Modules\Inventario\UnidadDimension\Actions\ToggleUnidadDimensionActivaAction;

//use App\Modules\Inventario\UnidadDimension\Tables\UnidadDimensionTableDefinition;

class UnidadDimensionTable extends TableComponent
{
    protected function tableClass(): string
    {
        return \App\Modules\inventario\UnidadDimension\Tables\UnidadDimensionTableDefinition::class;
    }

    public function toggleActiva(int $unidadDimensionId): void
    {
        $unidadDimension = UnidadDimension::findOrFail($unidadDimensionId);

        app(ToggleUnidadDimensionActivaAction::class)->execute($unidadDimension);
    }

    public function placeholder()
    {
        return view('components.loading-placeholder', [
            'message' => 'Cargando Unidades Dimensión...',
        ]);
    }
}