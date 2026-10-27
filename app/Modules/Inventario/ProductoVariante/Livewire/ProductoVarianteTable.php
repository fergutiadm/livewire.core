<?php

namespace App\Modules\Inventario\ProductoVariante\Livewire;

use App\Core\Tables\TableComponent;
//use App\Modules\Inventario\ProductoVariante\Tables\ProductoVarianteTableDefinition;

class ProductoVarianteTable extends TableComponent
{
    protected function tableClass(): string
    {
        return \App\Modules\inventario\ProductoVariante\Tables\ProductoVarianteTableDefinition::class;
    }
}