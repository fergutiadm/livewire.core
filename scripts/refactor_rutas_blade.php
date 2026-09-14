<?php

echo "==================================================\n";
echo "   REFACTORIZADOR DE RUTAS BLADE EN PHP (CQRS)    \n";
echo "==================================================\n\n";

// 1. Solicitar Módulo Origen con valor predeterminado (en minúsculas para Blade)
if (!isset($moduloOrigen)) {
    $moduloOrigenInput = trim(readline("1. MÓDULO ORIGEN de vistas [Por defecto: Inventario]: "));
    $moduloOrigen = empty($moduloOrigenInput) ? 'inventario' : strtolower($moduloOrigenInput);
} else {
    $moduloOrigen = strtolower($moduloOrigen);
}

// 2. Solicitar Módulo Destino
if (!isset($moduloDestino)) {
    $moduloDestinoInput = trim(readline("2. Ingresa el MÓDULO DESTINO (ej. Comercial): "));
    $moduloDestino = strtolower($moduloDestinoInput);
} else {
    $moduloDestino = strtolower($moduloDestino);
}

// 3. Solicitar Submódulo
if (!isset($submodulo)) {
    $submoduloInput = trim(readline("3. Ingresa el SUBMÓDULO / ENTIDAD (ej. Cliente): "));
    $submodulo = strtolower($submoduloInput);
} else {
    $submodulo = strtolower($submodulo);
}

// Validar campos obligatorios
if (empty($moduloDestino) || empty($submodulo)) {
    die("\n[Error] El Módulo Destino y el Submódulo son obligatorios. Proceso cancelado.\n");
}

// Volvemos a usar la versión PascalCase para buscar los archivos PHP correspondientes (en app/Modules/Comercial/Cliente)
$moduloDestinoClass = ucfirst($moduloDestino);
$submoduloClass = ucfirst($submodulo);

// Ruta hacia la carpeta del código PHP recién movido (donde están los componentes que llaman a las vistas)
$basePath = realpath(__DIR__ . '/../app/Modules');
$targetDir = strtolower("$basePath/$moduloDestinoClass/$submoduloClass");

if (!is_dir($targetDir)) {
    mkdir($targetDir);
    chmod($targetDir, 0777);
    //die("\n[Error] La carpeta de código PHP no existe en la ruta:\n$targetDir\nPor favor, asegúrate de haber corrido primero 'mover_modulo.php'.\n");
}

echo "\n--------------------------------------------------\n";
echo "Fase 1: Refactorizando llamadas view() en archivos PHP del submódulo...\n";
echo "--------------------------------------------------\n";

// Patrón de búsqueda: busca "modules.inventario.cliente" (con puntos de dot notation de Laravel)
$oldBladePath = "modules.$moduloOrigen.$submodulo";
$newBladePath = "modules.$moduloDestino.$submodulo";

$searchPattern = '/' . preg_quote($oldBladePath, '/') . '/i';

$directoryIterator = new RecursiveDirectoryIterator($targetDir);
$iterator = new RecursiveIteratorIterator($directoryIterator);
$filesUpdated = 0;

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $filePath = $file->getRealPath();
        $content = file_get_contents($filePath);

        if (preg_match($searchPattern, $content)) {
            // Reemplazar la ruta vieja de la vista por la nueva
            $content = preg_replace($searchPattern, $newBladePath, $content);
            file_put_contents($filePath, $content);

            $relativeRoute = str_replace($basePath, 'app/Modules', $filePath);
            echo "✔ Ruta Blade Actualizada en: $relativeRoute\n";
            $filesUpdated++;
        }
    }
}

echo "\n==================================================\n";
echo "¡Proceso completado! Se actualizaron las referencias en $filesUpdated archivos PHP.\n";
echo "==================================================\n";
