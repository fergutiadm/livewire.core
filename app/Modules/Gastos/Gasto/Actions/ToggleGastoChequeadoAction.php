<?php

namespace App\Modules\Gastos\Gasto\Actions;

use App\Models\Gasto;

class ToggleGastoChequeadoAction
{
    public function execute(Gasto $gasto): Gasto
    {
        $gasto->chequeado = ! $gasto->chequeado;
        $gasto->fecha_chequeado = $gasto->chequeado ? now()->format('Y-m-d') : NULL;
        $gasto->save();

        return $gasto->fresh();
    }
}
