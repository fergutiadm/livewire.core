<?php

namespace App\Modules\Finanzas\CuentaBancaria\Actions;

use App\Models\CuentaBancaria;

class ToggleCuentaBancariaActivaAction
{
    public function execute(CuentaBancaria $cuenta): CuentaBancaria
    {
        $cuenta->activa = ! $cuenta->activa;
        $cuenta->save();

        return $cuenta->fresh();
    }
}