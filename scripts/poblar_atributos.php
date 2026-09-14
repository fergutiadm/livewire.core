<?php

use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Atributo;
use App\Models\AtributoValor;
use App\Models\AtributableAtributo;
use App\Models\AtributableValor;
use Illuminate\Support\Facades\DB;

echo "\n========================================\n";
echo "POBLAR ATRIBUTOS - CATEGORIA 6\n";
echo "========================================\n\n";

DB::transaction(function () {

    /*
    |--------------------------------------------------------------------------
    | 1. CATEGORIA
    |--------------------------------------------------------------------------
    */

    $categoria = Categoria::findOrFail(6);

    echo "Categoria: {$categoria->id} - {$categoria->nombre}\n";

    /*
    |--------------------------------------------------------------------------
    | 2. CREAR ATRIBUTOS
    |--------------------------------------------------------------------------
    */

    $definiciones = [
        [
            'nombre' => 'Capacidad',
            'valores' => [
                '16 GB',
                '32 GB',
                '64 GB',
                '128 GB',
            ],
        ],
        [
            'nombre' => 'Color',
            'valores' => [
                'Negro',
                'Blanco',
                'Azul',
                'Rojo',
            ],
        ],
        [
            'nombre' => 'Marca',
            'valores' => [
                'Kingston',
                'SanDisk',
                'Samsung',
                'ADATA',
            ],
        ],
    ];

    foreach ($definiciones as $ordenAtributo => $definicion) {

        /*
         * No duplicar si ejecutamos el script nuevamente.
         */
        $atributo = Atributo::firstOrCreate(
            [
                'nombre' => $definicion['nombre'],
            ],
            [
                'orden_visual' => $ordenAtributo,
            ]
        );

        /*
         * Si ya existía, aseguramos su orden.
         */
        $atributo->update([
            'orden_visual' => $ordenAtributo,
        ]);

        echo "\nAtributo: {$atributo->id} - {$atributo->nombre}\n";

        /*
         |--------------------------------------------------------------------------
         | 3. CREAR VALORES DEL ATRIBUTO
         |--------------------------------------------------------------------------
         */

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
    }

    /*
    |--------------------------------------------------------------------------
    | 4. OBTENER LOS ATRIBUTOS
    |--------------------------------------------------------------------------
    */

    $atributos = Atributo::whereIn('nombre', [
        'Capacidad',
        'Color',
        'Marca',
    ])
        ->orderBy('orden_visual')
        ->get();

    /*
    |--------------------------------------------------------------------------
    | 5. ASOCIAR ATRIBUTOS A CATEGORIA 6
    |--------------------------------------------------------------------------
    */

    echo "\n========================================\n";
    echo "ASOCIANDO ATRIBUTOS A CATEGORIA\n";
    echo "========================================\n";

    foreach ($atributos as $orden => $atributo) {

        AtributableAtributo::updateOrCreate(
            [
                'atributo_id' => $atributo->id,
                'atributable_type' => Categoria::class,
                'atributable_id' => $categoria->id,
            ],
            [
                'orden_visual' => $orden,
            ]
        );

        echo "Categoria {$categoria->id} <- {$atributo->nombre}\n";
    }

    /*
    |--------------------------------------------------------------------------
    | 6. ASOCIAR VALORES A LA CATEGORIA
    |--------------------------------------------------------------------------
    |
    | Para la prueba vamos a darle a la categoría:
    |
    | Capacidad -> 16 GB, 32 GB, 64 GB
    | Color     -> Negro, Blanco
    | Marca     -> Kingston, SanDisk
    |
    */

    $valoresCategoria = [
        'Capacidad' => [
            '16 GB',
            '32 GB',
            '64 GB',
        ],

        'Color' => [
            'Negro',
            'Blanco',
        ],

        'Marca' => [
            'Kingston',
            'SanDisk',
        ],
    ];

    echo "\n========================================\n";
    echo "ASOCIANDO VALORES A CATEGORIA\n";
    echo "========================================\n";

    foreach ($valoresCategoria as $nombreAtributo => $valores) {

        $atributo = $atributos->firstWhere(
            'nombre',
            $nombreAtributo
        );

        if (!$atributo) {
            continue;
        }

        foreach ($valores as $orden => $textoValor) {

            $atributoValor = AtributoValor::where(
                'atributo_id',
                $atributo->id
            )
                ->where('valor', $textoValor)
                ->first();

            if (!$atributoValor) {
                continue;
            }

            AtributableValor::updateOrCreate(
                [
                    'atributo_valor_id' => $atributoValor->id,
                    'atributable_type' => Categoria::class,
                    'atributable_id' => $categoria->id,
                ],
                [
                    'orden_visual' => $orden,
                ]
            );

            echo "Categoria {$categoria->id} <- {$nombreAtributo}: {$textoValor}\n";
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 7. MOSTRAR PRODUCTOS DE LA CATEGORIA
    |--------------------------------------------------------------------------
    */

    $productos = Producto::where('categoria_id', $categoria->id)
        ->orderBy('id')
        ->get();

    echo "\n========================================\n";
    echo "PRODUCTOS DE LA CATEGORIA\n";
    echo "========================================\n";

    foreach ($productos as $producto) {
        echo "Producto {$producto->id}: {$producto->nombre}\n";
    }

    /*
    |--------------------------------------------------------------------------
    | 8. PREPARAR UN PRODUCTO CON ATRIBUTOS PROPIOS
    |--------------------------------------------------------------------------
    |
    | Esto nos permitirá probar posteriormente que:
    |
    | Categoria
    |   Capacidad
    |   Color
    |   Marca
    |
    | Producto
    |   Capacidad
    |
    | Es decir, el producto tiene atributos propios y no debe
    | comportarse igual que uno que solamente recibe sugerencias.
    |
    */

    $productoPropio = $productos->first();

    if ($productoPropio) {

        $capacidad = Atributo::where(
            'nombre',
            'Capacidad'
        )->first();

        $valor32 = AtributoValor::where(
            'atributo_id',
            $capacidad->id
        )
            ->where('valor', '32 GB')
            ->first();

        if ($capacidad && $valor32) {

            AtributableAtributo::updateOrCreate(
                [
                    'atributo_id' => $capacidad->id,
                    'atributable_type' => Producto::class,
                    'atributable_id' => $productoPropio->id,
                ],
                [
                    'orden_visual' => 0,
                ]
            );

            AtributableValor::updateOrCreate(
                [
                    'atributo_valor_id' => $valor32->id,
                    'atributable_type' => Producto::class,
                    'atributable_id' => $productoPropio->id,
                ],
                [
                    'orden_visual' => 0,
                ]
            );

            echo "\nProducto {$productoPropio->id} preparado con atributo propio:\n";
            echo "   Capacidad -> 32 GB\n";
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 9. RESUMEN
    |--------------------------------------------------------------------------
    */

    echo "\n========================================\n";
    echo "RESUMEN\n";
    echo "========================================\n";

    echo "Categoria: {$categoria->id} - {$categoria->nombre}\n";

    echo "\nAtributos de categoria:\n";

    $categoriaAtributos = AtributableAtributo::with('atributo')
        ->where('atributable_type', Categoria::class)
        ->where('atributable_id', $categoria->id)
        ->orderBy('orden_visual')
        ->get();

    foreach ($categoriaAtributos as $item) {
        echo "  {$item->orden_visual}. {$item->atributo->nombre}\n";
    }

    echo "\nValores de categoria:\n";

    $categoriaValores = AtributableValor::with('atributoValor.atributo')
        ->where('atributable_type', Categoria::class)
        ->where('atributable_id', $categoria->id)
        ->orderBy('orden_visual')
        ->get();

    foreach ($categoriaValores as $item) {
        echo "  {$item->atributoValor->atributo->nombre}";
        echo " -> {$item->atributoValor->valor}\n";
    }

    echo "\nProductos: {$productos->count()}\n";

    echo "\n========================================\n";
    echo "COMPLETADO\n";
    echo "========================================\n\n";
});
