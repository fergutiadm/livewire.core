<?php

namespace App\Modules\Inventario\ProductoUnidad\Livewire;

use App\Core\Tables\TableComponent;
//use App\Modules\Inventario\ProductoUnidad\Tables\ProductoUnidadTableDefinition;

class ProductoUnidadTable extends TableComponent
{
    protected function tableClass(): string
    {
        return \App\Modules\inventario\ProductoUnidad\Tables\ProductoUnidadTableDefinition::class;
    }
}