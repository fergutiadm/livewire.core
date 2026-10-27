<?php

namespace Database\Seeders;

use App\Models\SetupConfig;
use App\Modules\Configuracion\SetupConfig\Services\SetupConfigService;
use Illuminate\Database\Seeder;

class SetupConfigSeeder extends Seeder
{
    public function run(): void
    {
        $configuraciones = [
            [
                'nombre' => 'Elementos por página',
                'item_id' => 'items_por_pagina',
                'descripcion' => 'Cantidad de registros por página.',
                'tipo' => SetupConfig::TIPO_NUMERO,
                'valor' => '15',
                'valor_inicial' => '15',
                'valor_minimo' => '5',
                'valor_maximo' => '100',
            ],
            [
                'nombre' => 'Tasa de cambio de prueba',
                'item_id' => 'tasa_cambio_prueba',
                'descripcion' => 'Valor ficticio para probar cálculos.',
                'tipo' => SetupConfig::TIPO_NUMERO,
                'valor' => '350.25',
                'valor_inicial' => '350.25',
                'valor_minimo' => '1',
                'valor_maximo' => '999999',
            ],
            [
                'nombre' => 'Modo mantenimiento',
                'item_id' => 'modo_mantenimiento',
                'descripcion' => '0 desactivado; 1 activado.',
                'tipo' => SetupConfig::TIPO_NUMERO,
                'valor' => '0',
                'valor_inicial' => '0',
                'valor_minimo' => '0',
                'valor_maximo' => '1',
            ],
            [
                'nombre' => 'Nombre de empresa de prueba',
                'item_id' => 'empresa_nombre_demo',
                'descripcion' => 'Nombre ficticio para probar la lectura de texto.',
                'tipo' => SetupConfig::TIPO_TEXTO,
                'valor' => 'Empresa Demo',
                'valor_inicial' => null,
                'valor_minimo' => null,
                'valor_maximo' => null,
            ],
            [
                'nombre' => 'Monedas habilitadas',
                'item_id' => 'monedas_habilitadas',
                'descripcion' => 'Lista de códigos de monedas para pruebas.',
                'tipo' => SetupConfig::TIPO_ARREGLO,
                'valor' => 'CUP|USD|EUR',
                'valor_inicial' => null,
                'valor_minimo' => null,
                'valor_maximo' => null,
            ],
            [
                'nombre' => 'Roles de inventario',
                'item_id' => 'roles_inventario_demo',
                'descripcion' => 'Lista ficticia de roles para pruebas.',
                'tipo' => SetupConfig::TIPO_ARREGLO,
                'valor' => 'admin|inventario|supervisor',
                'valor_inicial' => null,
                'valor_minimo' => null,
                'valor_maximo' => null,
            ],
        ];

        foreach ($configuraciones as $configuracion) {
            SetupConfig::updateOrCreate(
                ['item_id' => $configuracion['item_id']],
                $configuracion
            );
        }

        app(SetupConfigService::class)->forgetCache();
    }
}
