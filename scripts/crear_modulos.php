<?php

// Definir la ruta base hacia app/Modules (subiendo un nivel desde la carpeta scripts)
$basePath = __DIR__ . '/../app/Modules';

// Estructura de módulos y submódulos basada en tus requerimientos
$modules = [
    'Inventario' => ['Atributo', 'Categoria', 'Local', 'Producto', 'MovimientoInventario'],
    'Comercial' => ['Cliente', 'Gestor'],
    'Finanzas' => ['Moneda', 'TarjetaMagnetica'],
    'Contabilidad' => ['PeriodoContable'],
    'Seguridad' => ['User']
];

echo "Iniciando creación de estructura modular...\n";

foreach ($modules as $module => $submodules) {
    foreach ($submodules as $submodule) {
        // Ruta completa del submódulo
        $dirPath = "$basePath/$module/$submodule";

        // Crear las carpetas si no existen de forma recursiva (true)
        if (!is_dir($dirPath)) {
            if (mkdir($dirPath, 0755, true)) {
                echo "Creado: app/Modules/$module/$submodule\n";
            } else {
                echo "Error al crear: app/Modules/$module/$submodule\n";
            }
        } else {
            echo "Ya existe: app/Modules/$module/$submodule\n";
        }
    }
}

echo "\n¡Estructura de carpetas generada con éxito!\n";
