<?php

declare(strict_types=1);

/**
 * Lista los Models Eloquent del proyecto Laravel.
 *
 * Uso:
 *
 *     php scripts/listModels.php
 *
 * Resultado:
 *
 *     App\Models\User
 *         → app/Models/User.php
 *
 *     App\Modules\Inventario\Producto\Models\Producto
 *         → app/Modules/Inventario/Producto/Models/Producto.php
 */

$project = dirname(__DIR__);
$appPath = $project . DIRECTORY_SEPARATOR . 'app';

if (!is_dir($appPath)) {
    echo "ERROR: No existe el directorio app/.\n";
    exit(1);
}

$models = [];

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator(
        $appPath,
        FilesystemIterator::SKIP_DOTS
    )
);

foreach ($iterator as $file) {

    if (!$file->isFile()) {
        continue;
    }

    if (strtolower($file->getExtension()) !== 'php') {
        continue;
    }

    $content = file_get_contents($file->getPathname());

    if ($content === false) {
        continue;
    }

    /*
    |--------------------------------------------------------------------------
    | Obtener namespace
    |--------------------------------------------------------------------------
    */

    if (!preg_match(
        '/namespace\s+([^;]+);/i',
        $content,
        $namespaceMatch
    )) {
        continue;
    }

    /*
    |--------------------------------------------------------------------------
    | Obtener clase que extiende directamente de Model
    |--------------------------------------------------------------------------
    */

    if (!preg_match(
        '/\bclass\s+([A-Za-z_][A-Za-z0-9_]*)\s+extends\s+Model\b/',
        $content,
        $classMatch
    )) {
        continue;
    }

    $namespace = trim($namespaceMatch[1]);
    $className = $classMatch[1];

    $fullClassName = $namespace . '\\' . $className;

    /*
    |--------------------------------------------------------------------------
    | Ruta relativa al proyecto
    |--------------------------------------------------------------------------
    */

    $relativePath = str_replace(
        $project . DIRECTORY_SEPARATOR,
        '',
        $file->getPathname()
    );

    $models[] = [
        'class' => $fullClassName,
        'file' => str_replace(
            DIRECTORY_SEPARATOR,
            '/',
            $relativePath
        ),
    ];
}

/*
|--------------------------------------------------------------------------
| Ordenar por nombre completo de clase
|--------------------------------------------------------------------------
*/

usort(
    $models,
    fn (array $a, array $b): int =>
        strcmp($a['class'], $b['class'])
);

/*
|--------------------------------------------------------------------------
| Mostrar resultado
|--------------------------------------------------------------------------
*/

if (empty($models)) {
    echo "No se encontraron Models Eloquent.\n";
    exit(0);
}

foreach ($models as $model) {
    echo $model['class']
        . '  →  '
        . $model['file']
        . PHP_EOL;
}

echo PHP_EOL;
echo 'Total: ' . count($models) . " Models\n";