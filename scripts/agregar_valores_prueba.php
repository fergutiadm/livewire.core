<?php

use App\Models\Atributo;
use App\Models\AtributoValor;

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "\n========================================\n";
echo "AGREGAR VALORES DE PRUEBA\n";
echo "========================================\n\n";

$datos = [
    1 => [
        '16 GB Plus',
        '128 GB',
        '256 GB',
    ],

    2 => [
        'Rojo',
        'Azul',
        'Verde',
    ],

    3 => [
        'Corsair',
        'Crucial',
        'Samsung',
    ],
];

foreach ($datos as $atributoId => $valores) {

    $atributo = Atributo::find($atributoId);

    if (!$atributo) {
        echo "Atributo {$atributoId} no encontrado.\n\n";
        continue;
    }

    echo "Atributo: {$atributo->id} - {$atributo->nombre}\n";

    foreach ($valores as $valorTexto) {

        $valor = AtributoValor::firstOrCreate(
            [
                'atributo_id' => $atributo->id,
                'valor' => $valorTexto,
            ],
            [
                'orden_visual' => 0,
            ]
        );

        echo "   Valor: {$valor->id} - {$valor->valor}\n";
    }

    echo "\n";
}

echo "========================================\n";
echo "COMPLETADO\n";
echo "========================================\n\n";