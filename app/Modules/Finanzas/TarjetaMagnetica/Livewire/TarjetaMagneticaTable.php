<?php

namespace App\Modules\Finanzas\TarjetaMagnetica\Livewire;

use App\Core\Tables\TableComponent;
//use App\Modules\Finanzas\TarjetaMagnetica\Tables\TarjetaMagneticaTableDefinition;

class TarjetaMagneticaTable extends TableComponent
{
    protected function tableClass(): string
    {
        return \App\Modules\Finanzas\TarjetaMagnetica\Tables\TarjetaMagneticaTableDefinition::class;
    }

    public function placeholder()
    {
        return view('components.loading-placeholder', [
            'message' => 'Cargando tarjetas magnéticas...',
        ]);
    }
}