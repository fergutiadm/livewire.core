<?php

namespace App\Modules\Finanzas\Banco\Actions;

use App\Models\Banco;
use App\Models\SetupConfig;
use App\Modules\Configuracion\SetupConfig\Services\SetupConfigService;

class ToggleBancoPorDefectoAction
{
    public function execute(Banco $banco): Banco
    {
        $setupConfig = SetupConfig::query()->firstWhere('item_id', 'banco_default');
        if($setupConfig){
            $valor = ($setupConfig->valor !== $banco->codigo_swift) ? $banco->codigo_swift : '';
            $setupConfig->valor = $valor;
            $setupConfig->valor_inicial = $valor;

            $setupConfig->save();
        }else{
            SetupConfig::create(
                [
                    'nombre' => 'Banco por defecto',
                    'item_id' => 'banco_default',
                    'descripcion' => 'Banco por defecto',
                    'tipo' => 2,
                    'valor' => $banco->codigo_swift,
                    'valor_inicial' => $banco->codigo_swift,
                ]
            );

        }

        app(SetupConfigService::class)->forgetCache();

        return $banco->fresh();
    }
}
