<?php

namespace App\Modules\Seguridad\User\Livewire;

use App\Core\Tables\TableComponent;
//use App\Modules\Seguridad\User\Tables\UserTableDefinition;

class UserTable extends TableComponent
{
    protected function tableClass(): string
    {
        return \App\Modules\Seguridad\User\Tables\UserTableDefinition::class;
    }

    public function placeholder()
    {
        return view('components.loading-placeholder', [
            'message' => 'Cargando usuarios...',
        ]);
    }
}