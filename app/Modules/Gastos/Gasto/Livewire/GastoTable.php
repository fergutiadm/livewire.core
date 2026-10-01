<?php

namespace App\Modules\Gastos\Gasto\Livewire;

use App\Core\Tables\TableComponent;
//use App\Modules\Gastos\Gasto\Tables\GastoTableDefinition;

class GastoTable extends TableComponent
{
    protected function tableClass(): string
    {
        return \App\Modules\gastos\Gasto\Tables\GastoTableDefinition::class;
    }
}