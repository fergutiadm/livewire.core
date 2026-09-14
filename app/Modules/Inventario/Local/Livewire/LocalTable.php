<?php

namespace App\Modules\Inventario\Local\Livewire;

use App\Core\Tables\TableComponent;

class LocalTable extends TableComponent
{
    protected function tableClass(): string
    {
        return \App\Modules\inventario\Local\Tables\LocalTableDefinition::class;
    }

    public function placeholder()
    {
        return view('components.loading-placeholder', [
            'message' => 'Cargando locales...',
        ]);
    }
}