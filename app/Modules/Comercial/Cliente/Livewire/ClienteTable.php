<?php

namespace App\Modules\Comercial\Cliente\Livewire;

use App\Core\Tables\TableComponent;
//use App\Modules\Comercial\Cliente\Tables\ClienteTableDefinition;

class ClienteTable extends TableComponent
{
    protected function tableClass(): string
    {
        return \App\Modules\Comercial\Cliente\Tables\ClienteTableDefinition::class;
    }

    public function placeholder()
    {
        return view('components.loading-placeholder', [
            'message' => 'Cargando clientes...',
        ]);
    }
}