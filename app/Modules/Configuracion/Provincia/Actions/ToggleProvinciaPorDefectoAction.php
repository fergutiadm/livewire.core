<?php

namespace App\Modules\Configuracion\Provincia\Actions;

use App\Models\Provincia;
use App\Models\SetupConfig;
use App\Modules\Configuracion\SetupConfig\Services\SetupConfigService;

class ToggleProvinciaPorDefectoAction
{
    public function execute(Provincia $provincia): Provincia
    {
        $setupConfig = SetupConfig::query()->firstWhere('item_id', 'provincia_default');
        if($setupConfig){
            $valor = ($setupConfig->valor !== $provincia->slug) ? $provincia->slug : '';
            $setupConfig->valor = $valor;
            $setupConfig->valor_inicial = $valor;

            $setupConfig->save();
        }else{
            SetupConfig::create(
                [
                    'nombre' => 'Provincia por defecto',
                    'item_id' => 'provincia_default',
                    'descripcion' => 'Provincia por defecto',
                    'tipo' => 2,
                    'valor' => $provincia->slug,
                    'valor_inicial' => $provincia->slug,
                ]
            );

        }

        app(SetupConfigService::class)->forgetCache();

        return $provincia->fresh();
    }
}
