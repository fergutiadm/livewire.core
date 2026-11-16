<?php

namespace Database\Seeders;

use App\Models\Banco;
use Illuminate\Database\Seeder;

class BancoSeeder extends Seeder
{
    private const BANCOS = [
        [
            'nombre' => 'Banco Popular de Ahorro',
            'alias' => 'BPA',
            'codigo_swift' => 'BPAHCUHH',
            'telefonos' => null,
        ],
        [
            'nombre' => 'Banco de Crédito y Comercio',
            'alias' => 'BANDEC',
            'codigo_swift' => 'BDCRCUHH',
            'telefonos' => null,
        ],
        [
            'nombre' => 'Banco Metropolitano S.A.',
            'alias' => 'Banmet',
            'codigo_swift' => 'BMNBCUHH',
            'telefonos' => '7801-3418, 7801-3333 ext. 1221 y 1124',
        ],
        [
            'nombre' => 'Banco Nacional de Cuba',
            'alias' => 'BNC',
            'codigo_swift' => 'BNACCUHH',
            'telefonos' => null,
        ],
        [
            'nombre' => 'Banco Financiero Internacional S.A.',
            'alias' => 'BFI',
            'codigo_swift' => 'BFICCUHH',
            'telefonos' => null,
        ],
        [
            'nombre' => 'Banco Internacional de Comercio S.A.',
            'alias' => 'BICSA',
            'codigo_swift' => 'BIDCCUHH',
            'telefonos' => null,
        ],
        [
            'nombre' => 'Banco Exterior de Cuba',
            'alias' => 'BEC',
            'codigo_swift' => 'BECUCUHH',
            'telefonos' => null,
        ],
        [
            'nombre' => 'Banco de Inversiones S.A.',
            'alias' => null,
            'codigo_swift' => 'BICUCUHH',
            'telefonos' => null,
        ],
        [
            'nombre' => 'Banco de Venezuela S.A.',
            'alias' => 'BDV',
            'codigo_swift' => 'INDSCUHH',
            'telefonos' => null,
        ],
    ];

    public function run(): void
    {
        foreach (self::BANCOS as $datos) {
            Banco::updateOrCreate(
                ['nombre' => $datos['nombre']],
                [
                    'alias' => $datos['alias'],
                    'codigo_swift' => $datos['codigo_swift'],
                    'telefonos' => $datos['telefonos'],
                    'activo' => true,
                ]
            );
        }
    }
}
