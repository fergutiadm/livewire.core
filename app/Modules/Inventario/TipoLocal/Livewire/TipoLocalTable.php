<?php

namespace App\Modules\Inventario\TipoLocal\Livewire;

use App\Core\Tables\TableComponent;
use App\Models\TipoLocal;
use App\Modules\Inventario\TipoLocal\Actions\ToggleTipoLocalActivoAction;

//use App\Modules\Inventario\TipoLocal\Tables\TipoLocalTableDefinition;

class TipoLocalTable extends TableComponent
{
    protected function tableClass(): string
    {
        return \App\Modules\inventario\TipoLocal\Tables\TipoLocalTableDefinition::class;
    }

    public function toggleActivo(int $tipoLocalId): void
    {
        $tipoLocal = TipoLocal::findOrFail($tipoLocalId);

        app(ToggleTipoLocalActivoAction::class)->execute($tipoLocal);
    }

    public function placeholder()
    {
        return view('components.loading-placeholder', [
            'message' => 'Cargando tipos de local...',
        ]);
    }
}