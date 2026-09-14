<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Atributo;
use App\Models\AtributoValor;
use Illuminate\Support\Facades\DB;

echo "\n========================================\n";
echo "AGREGAR ATRIBUTOS DE PRUEBA\n";
echo "========================================\n\n";

DB::transaction(function () {

    $definiciones = [
        [
            'nombre' => 'Tipo de almacenamiento',
            'valores' => [
                'SSD',
                'HDD',
                'NVMe',
                'eMMC',
            ],
        ],
        [
            'nombre' => 'Procesador',
            'valores' => [
                'Intel',
                'AMD',
                'Apple',
                'Qualcomm',
            ],
        ],
        [
            'nombre' => 'Sistema operativo',
            'valores' => [
                'Windows',
                'Linux',
                'macOS',
                'Android',
            ],
        ],
    ];

    foreach ($definiciones as $ordenAtributo => $definicion) {

        $atributo = Atributo::firstOrCreate(
            [
                'nombre' => $definicion['nombre'],
            ],
            [
                'orden_visual' => $ordenAtributo + 3,
            ]
        );

        $atributo->update([
            'orden_visual' => $ordenAtributo + 3,
        ]);

        echo "Atributo: {$atributo->id} - {$atributo->nombre}\n";

        foreach ($definicion['valores'] as $ordenValor => $textoValor) {

            $valor = AtributoValor::firstOrCreate(
                [
                    'atributo_id' => $atributo->id,
                    'valor' => $textoValor,
                ],
                [
                    'orden_visual' => $ordenValor,
                ]
            );

            $valor->update([
                'orden_visual' => $ordenValor,
            ]);

            echo "   Valor: {$valor->id} - {$valor->valor}\n";
        }

        echo "\n";
    }

    echo "========================================\n";
    echo "COMPLETADO\n";
    echo "========================================\n\n";
});