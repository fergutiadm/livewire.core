<?php

namespace App\Modules\Finanzas\BancoSucursal\Actions;

use App\Models\BancoSucursal;

class ToggleBancoSucursalActivaAction
{
    public function execute(BancoSucursal $sucursal): BancoSucursal
    {
        $sucursal->activa = ! $sucursal->activa;
        $sucursal->save();

        return $sucursal->fresh();
    }
}
