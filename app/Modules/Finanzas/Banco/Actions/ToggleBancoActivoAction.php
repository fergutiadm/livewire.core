<?php

namespace App\Modules\Finanzas\Banco\Actions;

use App\Models\Banco;

class ToggleBancoActivoAction
{
    public function execute(Banco $banco): Banco
    {
        $banco->activo = ! $banco->activo;
        $banco->save();

        return $banco->fresh();
    }
}
