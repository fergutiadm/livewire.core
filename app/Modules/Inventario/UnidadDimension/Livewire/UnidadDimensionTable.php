<?php

namespace App\Modules\Inventario\UnidadDimension\Livewire;

use App\Core\Tables\TableComponent;
//use App\Modules\Inventario\UnidadDimension\Tables\UnidadDimensionTableDefinition;

class UnidadDimensionTable extends TableComponent
{
    protected function tableClass(): string
    {
        return \App\Modules\inventario\UnidadDimension\Tables\UnidadDimensionTableDefinition::class;
    }
}