<?php

echo "==================================================\n";
echo "    MOVEDOR DE VISTAS BLADE (SUBMÓDULOS)          \n";
echo "==================================================\n\n";

// Definir origen fijo de las vistas (inventario en minúsculas)
$moduloOrigen = 'inventario';

// 1. Solicitar Submódulo/Entidad a mover
$submoduloInput = trim(readline("1. Ingresa el SUBMÓDULO / ENTIDAD a mover (ej. Cliente): "));
// Convertir a minúsculas ya que las carpetas de vistas suelen ir en minúsculas
$submodulo = strtolower($submoduloInput);

// 2. Solicitar Módulo Destino
$moduloDestinoInput = trim(readline("2. Ingresa el MÓDULO DESTINO (ej. Comercial): "));
$moduloDestino = strtolower($moduloDestinoInput);

if (empty($submodulo) || empty($moduloDestino)) {
    die("\n[Error] El Submódulo y el Módulo Destino son obligatorios. Proceso cancelado.\n");
}

// Rutas base hacia las vistas
$basePath = realpath(__DIR__ . '/../resources/views/modules');
$dirOrigen  = "$basePath/$moduloOrigen/$submodulo";
$dirDestino = strtolower("$basePath/$moduloDestino/$submodulo");

// Validar que la carpeta de origen exista
if (!is_dir($dirOrigen)) {
    die("\n[Error] La carpeta de origen no existe en: resources/views/modules/$moduloOrigen/$submodulo\n");
}

// Validar que el directorio del módulo destino exista (ej: comercial)
if (!is_dir("$basePath/$moduloDestino")) {
    mkdir($dirDestino);
    chmod($dirDestino, 0777);
    //die("\n[Error] La carpeta del módulo destino principal no existe: resources/views/modules/$moduloDestino\n");
}

// Validar que no exista ya en el destino
if (is_dir($dirDestino)) {
    die("\n[Error] Ya existe una carpeta de vistas para '$submodulo' dentro de '$moduloDestino'. Proceso cancelado.\n");
}

echo "\nMoviendo resources/views/modules/$moduloOrigen/$submodulo -> resources/views/modules/$moduloDestino/$submodulo...\n";

// SOLUCIÓN PARA WINDOWS EPERM
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    exec('attrib -R "' . str_replace('/', '\\', $dirOrigen) . '" /S /D');
}

// Realizar el movimiento físico de la carpeta de vistas
if (rename($dirOrigen, $dirDestino)) {
    echo "✔ Vistas Blade movidas con éxito.\n";

    echo "\n--------------------------------------------------\n";
    $ejecutarRefactor = strtolower(trim(readline("¿Deseas actualizar las rutas de renderizado (view) en tus archivos PHP ahora mismo? (s/n): ")));

    if ($ejecutarRefactor === 's' || $ejecutarRefactor === 'si') {
        $scriptRefactor = __DIR__ . '/refactor_rutas_blade.php';
        if (file_exists($scriptRefactor)) {
            echo "\nEjecutando refactor_rutas_blade.php...\n";
            include $scriptRefactor;
        } else {
            echo "[Aviso] No se encontró el script 'refactor_rutas_blade.php' en la carpeta scripts.\n";
        }
    }
} else {
    echo "\n[Error] No se pudo mover la carpeta de vistas. Asegúrate de que ningún programa la esté usando.\n";
}
