<?php

echo "==================================================\n";
echo "   REFACTORIZADOR GLOBAL DE NAMESPACES (CQRS)     \n";
echo "==================================================\n\n";

// 1. Solicitar Módulo Origen con valor predeterminado
$moduloOrigen = trim(readline("1. Mergear MÓDULO ORIGEN [Por defecto: Inventario]: "));
if (empty($moduloOrigen)) {
    $moduloOrigen = 'Inventario';
}

// 2. Solicitar Módulo Destino
$moduloDestino = trim(readline("2. Ingresa el MÓDULO DESTINO (ej. Comercial): "));

// 3. Solicitar Submódulo
$submodulo = trim(readline("3. Ingresa el SUBMÓDULO / ENTIDAD (ej. Cliente): "));

// Validar campos obligatorios restantes
if (empty($moduloDestino) || empty($submodulo)) {
    die("\n[Error] El Módulo Destino y el Submódulo son obligatorios. Proceso cancelado.\n");
}

// Ruta base del proyecto hacia app/Modules
$basePath = realpath(__DIR__ . '/../app/Modules');
$targetDir = "$basePath/$moduloDestino/$submodulo";

// Verificar que la carpeta realmente exista en el destino final
if (!is_dir($targetDir)) {
    die("\n[Error] La carpeta de destino no existe en la ruta:\n$targetDir\nPor favor, mueve la carpeta físicamente en Windows antes de ejecutar este script.\n");
}

echo "\n--------------------------------------------------\n";
echo "Fase 1: Refactorizando archivos internos del submódulo...\n";
echo "--------------------------------------------------\n";

$oldNamespace = "App\\Modules\\$moduloOrigen\\$submodulo";
$newNamespace = "App\\Modules\\$moduloDestino\\$submodulo";

// Expresión regular insensible a mayúsculas/minúsculas para el namespace base del submódulo
$searchPattern = '/' . preg_quote($oldNamespace, '/') . '/i';

$directoryIterator = new RecursiveDirectoryIterator($targetDir);
$iterator = new RecursiveIteratorIterator($directoryIterator);
$filesUpdated = 0;

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $filePath = $file->getRealPath();
        $fileName = $file->getFilename();
        $content = file_get_contents($filePath);
        $fileChanged = false;

        // 1. Caso Especial: Ajuste insensible a mayúsculas de tableClass() en [Entidad]Table.php
        if ($fileName === "{$submodulo}Table.php") {
            $tableDefinitionVieja = "App\\Modules\\{$moduloOrigen}\\{$submodulo}\\Tables\\{$submodulo}TableDefinition";
            $tableDefinitionNueva = "App\\Modules\\{$moduloDestino}\\{$submodulo}\\Tables\\{$submodulo}TableDefinition";

            $posicion = stripos($content, $tableDefinitionVieja);
            if ($posicion !== false) {
                $cadenaExactaEnArchivo = substr($content, $posicion, strlen($tableDefinitionVieja));
                $content = str_replace($cadenaExactaEnArchivo, $tableDefinitionNueva, $content);
                $fileChanged = true;
            }
        }

        // 2. Reemplazo general en namespaces y dependencias internas (use)
        if (preg_match($searchPattern, $content)) {
            $content = preg_replace($searchPattern, $newNamespace, $content);
            $fileChanged = true;
        }

        if ($fileChanged) {
            file_put_contents($filePath, $content);
            $relativeRoute = str_replace($basePath, 'app/Modules', $filePath);
            echo "✔ Actualizado: $relativeRoute\n";
            $filesUpdated++;
        }
    }
}

echo "\n--------------------------------------------------\n";
echo "Fase 2: Refactorizando archivos de configuración global...\n";
echo "--------------------------------------------------\n";

// Rutas de archivos globales de la aplicación
$routesFile = realpath(__DIR__ . '/../routes/web.php');
$providerFile = realpath(__DIR__ . '/../app/Providers/AppServiceProvider.php');

$archivosGlobales = [
    'routes/web.php' => $routesFile,
    'app/Providers/AppServiceProvider.php' => $providerFile
];

foreach ($archivosGlobales as $nombreVisual => $rutaCompleta) {
    if ($rutaCompleta && file_exists($rutaCompleta)) {
        $content = file_get_contents($rutaCompleta);

        if (preg_match($searchPattern, $content)) {
            // Reemplaza de forma segura ignorando mayúsculas/minúsculas los imports del submódulo en el archivo global
            $content = preg_replace($searchPattern, $newNamespace, $content);
            file_put_contents($rutaCompleta, $content);
            echo "✔ Actualizado Global: $nombreVisual\n";
            $filesUpdated++;
        } else {
            echo "• Sin cambios necesarios en: $nombreVisual\n";
        }
    } else {
        echo "⚠ No se pudo encontrar el archivo: $nombreVisual\n";
    }
}

echo "\n==================================================\n";
echo "¡Proceso completado! Se actualizaron $filesUpdated archivos en total.\n";
echo "==================================================\n";
