<?php

echo "==================================================\n";
echo "    MOVEDOR DE SUBMÓDULOS (CON CONTROL DE EPERM)   \n";
echo "==================================================\n\n";

// Definir origen fijo (Inventario)
$moduloOrigen = 'Inventario';

// 1. Solicitar Submódulo/Entidad a mover
$submodulo = trim(readline("1. Ingresa el SUBMÓDULO / ENTIDAD a mover (ej. Cliente): "));

// 2. Solicitar Módulo Destino
$moduloDestino = trim(readline("2. Ingresa el MÓDULO DESTINO (ej. Comercial): "));

if (empty($submodulo) || empty($moduloDestino)) {
    die("\n[Error] El Submódulo y el Módulo Destino son obligatorios. Proceso cancelado.\n");
}

// Rutas base
$basePath = realpath(__DIR__ . '/../app/Modules');
$dirOrigen  = "$basePath/$moduloOrigen/$submodulo";
$dirDestino = "$basePath/$moduloDestino/$submodulo";

// Validar que la carpeta de origen exista
if (!is_dir($dirOrigen)) {
    die("\n[Error] La carpeta de origen no existe en: app/Modules/$moduloOrigen/$submodulo\n");
}

// Validar que el directorio del módulo destino exista (ej: Comercial)
if (!is_dir("$basePath/$moduloDestino")) {
    die("\n[Error] El módulo de destino principal no existe: app/Modules/$moduloDestino\n");
}

// Validar que no exista ya en el destino
if (is_dir($dirDestino)) {
    die("\n[Error] Ya existe un submódulo '$submodulo' dentro de '$moduloDestino'. Proceso cancelado.\n");
}

echo "\nMoviendo app/Modules/$moduloOrigen/$submodulo -> app/Modules/$moduloDestino/$submodulo...\n";

// SOLUCIÓN PARA WINDOWS EPERM: Forzar la limpieza de atributos de solo lectura en la carpeta antes de mover
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    exec('attrib -R "' . str_replace('/', '\\', $dirOrigen) . '" /S /D');
}

// Realizar el movimiento físico de la carpeta
if (rename($dirOrigen, $dirDestino)) {
    echo "✔ Carpeta movida con éxito.\n";

    // Preguntar si desea ejecutar el refactorizador de namespaces inmediatamente
    echo "\n--------------------------------------------------\n";
    $ejecutarRefactor = strtolower(trim(readline("¿Deseas actualizar los namespaces en los archivos ahora mismo? (s/n): ")));

    if ($ejecutarRefactor === 's' || $ejecutarRefactor === 'si') {
        $scriptRefactor = __DIR__ . '/refactor_namespaces.php';
        if (file_exists($scriptRefactor)) {
            echo "\nEjecutando refactor_namespaces.php...\n";
            // Incluir y ejecutar el script anterior directamente pasando las variables
            // Para que funcione de forma fluida, modificaremos levemente el flujo en tu memoria
            include $scriptRefactor;
        } else {
            echo "[Aviso] No se encontró el script 'refactor_namespaces.php' en la carpeta scripts.\n";
        }
    }
} else {
    echo "\n[Error] No se pudo mover la carpeta. Asegúrate de que ningún programa (o terminal) la esté usando.\n";
}
