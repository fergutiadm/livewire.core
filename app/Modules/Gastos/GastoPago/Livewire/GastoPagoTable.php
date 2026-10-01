<?php

namespace App\Modules\Gastos\GastoPago\Livewire;

use App\Core\Tables\TableComponent;
//use App\Modules\Gastos\GastoPago\Tables\GastoPagoTableDefinition;

class GastoPagoTable extends TableComponent
{
    protected function tableClass(): string
    {
        return \App\Modules\gastos\GastoPago\Tables\GastoPagoTableDefinition::class;
    }
}