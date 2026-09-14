<?php

namespace App\Modules\Comercial\Gestor\Livewire;

use App\Core\Tables\TableComponent;
use App\Models\Gestor;
use App\Modules\Comercial\Gestor\Actions\ToggleGestorActivoAction;

class GestorTable extends TableComponent
{
    protected function tableClass(): string
    {
        return \App\Modules\Comercial\Gestor\Tables\GestorTableDefinition::class;
    }

    public function toggleActivo(int $gestorId): void
    {
        $gestor = Gestor::findOrFail($gestorId);

        app(ToggleGestorActivoAction::class)
            ->execute($gestor);
    }

    public function placeholder()
    {
        return view('components.loading-placeholder', [
            'message' => 'Cargando gestores...',
        ]);
    }
}