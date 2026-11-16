<?php

namespace App\Modules\Configuracion\Municipio\Actions;

use App\Models\Municipio;
use App\Models\SetupConfig;
use App\Modules\Configuracion\SetupConfig\Services\SetupConfigService;

class ToggleMunicipioPorDefectoAction
{
    public function execute(Municipio $municipio): Municipio
    {
        $setupConfig = SetupConfig::query()->firstWhere('item_id', 'municipio_default');
        if($setupConfig){
            $valor = ($setupConfig->valor !== $municipio->slug) ? $municipio->slug : '';
            $setupConfig->valor = $valor;
            $setupConfig->valor_inicial = $valor;

            $setupConfig->save();
        }else{
            SetupConfig::create(
                [
                    'nombre' => 'Municipio por defecto',
                    'item_id' => 'municipio_default',
                    'descripcion' => 'Municipio por defecto',
                    'tipo' => 2,
                    'valor' => $municipio->slug,
                ]
            );

        }

        app(SetupConfigService::class)->forgetCache();

        return $municipio->fresh();
    }
}
