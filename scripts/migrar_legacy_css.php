<?php

/**
 * Migración de clases CSS legacy → clases actuales.
 *
 * Procesa los Blade principales y sus tablas de los módulos legacy.
 *
 * IMPORTANTE:
 * - Crea una copia .bak antes de modificar.
 * - Solo reemplaza clases cuyo destino conocemos con seguridad.
 * - Al final muestra las clases legacy que todavía quedan.
 */

$basePath = dirname(__DIR__);

$viewsPath = $basePath . DIRECTORY_SEPARATOR . 'resources'
    . DIRECTORY_SEPARATOR . 'views'
    . DIRECTORY_SEPARATOR . 'livewire'
    . DIRECTORY_SEPARATOR . 'admin';


$entities = [
    'categorias',
    'atributos',
    'clientes',
    'locales',
    'periodos-contables',
    'tarjetas-magneticas',
    'usuarios',
];


/*
|--------------------------------------------------------------------------
| Mapeos seguros
|--------------------------------------------------------------------------
|
| Estos son equivalentes que ya tenemos definidos en app.css.
|
*/

$replacements = [

    // FORMULARIOS
    'panel-form-grid-2' => 'panel-form-grid-2',
    'panel-form-grid-3' => 'panel-form-grid-3',
    'panel-form-grid-4' => 'panel-form-grid-4',

    'panel-form-group'   => 'panel-form-group',
    'panel-form-label'   => 'panel-form-label',
    'panel-form-input'   => 'panel-form-input',
    'panel-form-actions' => 'panel-form-actions',
    'panel-form-error'   => 'panel-form-error',

    // BOTONES
    'btn-primary'   => 'btn-primary',
    'btn-secondary' => 'btn-secondary',
    'btn-danger'    => 'btn-danger',
    'btn-ghost'     => 'btn-ghost',
    'btn-sm'        => 'btn-sm',

    // TABLAS
    'table-base' => 'table-base',
    'table-head' => 'table-head',
    'table-row'  => 'table-row',
    'table-cell' => 'table-cell',
];


/*
|--------------------------------------------------------------------------
| Clases legacy conocidas que NO debemos reemplazar automáticamente
|--------------------------------------------------------------------------
|
| Estas necesitan revisión porque no conocemos todavía su equivalente
| estructural exacto.
|
*/

$legacyToReview = [
    'panel-form-tab-group-4',
    'panel-layout-3',
    'panel-left',
    'panel-right',

    'table-container',
    'table-header',

    'btn-primary-1',
    'btn-danger-1',
    'btn-warning',
    'btn-cancel',

    'listado-data-td',
    'listado-data',

    'flex-end',
];


/*
|--------------------------------------------------------------------------
| Utilidades
|--------------------------------------------------------------------------
*/

function backupFile(string $file): string
{
    $backup = $file . '.bak';

    if (!file_exists($backup)) {
        copy($file, $backup);
    }

    return $backup;
}


function replaceClass(string $content, string $old, string $new): string
{
    /*
     * Solo reemplazamos cuando old es una clase CSS.
     *
     * Evita modificar:
     * - nombres de variables
     * - textos
     * - nombres de métodos
     * - substrings de otras clases
     */

    return preg_replace(
        '/(?<![A-Za-z0-9_-])' . preg_quote($old, '/') . '(?![A-Za-z0-9_-])/',
        $new,
        $content
    );
}


function findLegacyClasses(string $content, array $classes): array
{
    $found = [];

    foreach ($classes as $class) {
        if (preg_match(
            '/(?<![A-Za-z0-9_-])' . preg_quote($class, '/') . '(?![A-Za-z0-9_-])/',
            $content
        )) {
            $found[] = $class;
        }
    }

    return $found;
}


/*
|--------------------------------------------------------------------------
| Procesamiento
|--------------------------------------------------------------------------
*/

echo PHP_EOL;
echo "==============================================" . PHP_EOL;
echo " MIGRACIÓN CSS LEGACY DE MÓDULOS" . PHP_EOL;
echo "==============================================" . PHP_EOL;
echo PHP_EOL;


$processedFiles = [];
$modifiedFiles = [];
$remainingLegacy = [];


foreach ($entities as $entity) {

    $files = [
        $viewsPath . DIRECTORY_SEPARATOR . $entity . '.blade.php',
        $viewsPath . DIRECTORY_SEPARATOR . 'tabla-' . $entity . '.blade.php',
    ];


    echo "----------------------------------------------" . PHP_EOL;
    echo "Módulo: {$entity}" . PHP_EOL;
    echo "----------------------------------------------" . PHP_EOL;


    foreach ($files as $file) {

        if (!file_exists($file)) {
            echo "  [SKIP] No existe: " . basename($file) . PHP_EOL;
            continue;
        }


        $processedFiles[] = $file;

        $original = file_get_contents($file);
        $content = $original;


        /*
         * Aplicar reemplazos seguros.
         */

        foreach ($replacements as $old => $new) {

            /*
             * Si old y new son iguales no hacemos nada.
             */

            if ($old === $new) {
                continue;
            }

            $content = replaceClass($content, $old, $new);
        }


        /*
         * Detectar clases legacy pendientes.
         */

        $legacyFound = findLegacyClasses(
            $content,
            $legacyToReview
        );


        if ($content !== $original) {

            backupFile($file);

            file_put_contents($file, $content);

            $modifiedFiles[] = $file;

            echo "  [OK] Modificado: " . basename($file) . PHP_EOL;

        } else {

            echo "  [--] Sin cambios: " . basename($file) . PHP_EOL;
        }


        /*
         * Registrar legacy pendiente.
         */

        if (!empty($legacyFound)) {

            $remainingLegacy[$file] = $legacyFound;

            echo "       Legacy pendiente:" . PHP_EOL;

            foreach ($legacyFound as $class) {
                echo "         - {$class}" . PHP_EOL;
            }
        }
    }

    echo PHP_EOL;
}


/*
|--------------------------------------------------------------------------
| Resumen
|--------------------------------------------------------------------------
*/

echo PHP_EOL;
echo "==============================================" . PHP_EOL;
echo " RESUMEN" . PHP_EOL;
echo "==============================================" . PHP_EOL;

echo PHP_EOL;

echo "Archivos procesados : " . count($processedFiles) . PHP_EOL;
echo "Archivos modificados: " . count($modifiedFiles) . PHP_EOL;
echo "Backups creados     : " . count($modifiedFiles) . PHP_EOL;

echo PHP_EOL;


if (!empty($remainingLegacy)) {

    echo "==============================================" . PHP_EOL;
    echo " LEGACY QUE REQUIERE REVISIÓN" . PHP_EOL;
    echo "==============================================" . PHP_EOL;
    echo PHP_EOL;


    foreach ($remainingLegacy as $file => $classes) {

        echo basename($file) . PHP_EOL;

        foreach ($classes as $class) {
            echo "  - {$class}" . PHP_EOL;
        }

        echo PHP_EOL;
    }

} else {

    echo "No quedaron clases legacy conocidas." . PHP_EOL;
}


echo PHP_EOL;
echo "Backups disponibles como *.blade.php.bak" . PHP_EOL;
echo PHP_EOL;