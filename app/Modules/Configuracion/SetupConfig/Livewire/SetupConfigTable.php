<?php

namespace App\Modules\Configuracion\SetupConfig\Livewire;

use App\Core\Tables\TableComponent;
use App\Modules\Configuracion\SetupConfig\Tables\SetupConfigTableDefinition;

class SetupConfigTable extends TableComponent
{
    protected function tableClass(): string
    {
        return SetupConfigTableDefinition::class;
    }

    public function placeholder()
    {
        return view('components.loading-placeholder', [
            'message' => 'Cargando configuraciones...',
        ]);
    }
}
