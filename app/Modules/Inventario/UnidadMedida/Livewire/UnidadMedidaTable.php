<?php

namespace App\Modules\Inventario\UnidadMedida\Livewire;

use App\Core\Tables\TableComponent;
//use App\Modules\Inventario\UnidadMedida\Tables\UnidadMedidaTableDefinition;

class UnidadMedidaTable extends TableComponent
{
    protected function tableClass(): string
    {
        return \App\Modules\inventario\UnidadMedida\Tables\UnidadMedidaTableDefinition::class;
    }
}