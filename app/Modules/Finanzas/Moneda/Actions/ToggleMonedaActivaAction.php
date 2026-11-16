<?php

namespace App\Modules\Finanzas\Moneda\Actions;

use App\Models\Moneda;
use App\Services\MonedaService;

class ToggleMonedaActivaAction
{
    public function execute(Moneda $moneda): Moneda
    {
        $moneda->activa = ! $moneda->activa;
        $moneda->save();

        app(MonedaService::class)->forgetCache();

        return $moneda->fresh();
    }
}
