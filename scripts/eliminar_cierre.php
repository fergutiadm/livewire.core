## 🗑️ 2. Script para ELIMINAR los archivos creados (`eliminar_cierre.php`)

Si decides dar marcha atrás y limpiar tu proyecto borrando exclusivamente las carpetas y ficheros inyectados por este módulo, crea un archivo llamado **`eliminar_cierre.php`** en la raíz de tu proyecto Laravel y ejecútalo con `php eliminar_cierre.php`:

```php
<?php
/**
 * Limpiador automático para remover el submódulo de Cierre Diario (PHP 8.1)
 */

// Lista exacta de archivos a remover
$archivos_a_borrar = [
    "database/migrations/2026_10_07_000002_create_cierre_diario_desgloses_table.php",
    "database/migrations/2026_10_07_000003_create_cierre_detalles_table.php",
    "app/Models/CierreDiarioDesglose.php",
    "app/Models/CierreDetalle.php",
    "app/Modules/Admin/Cierre/Livewire/CierrePage.php",
    "app/Modules/Admin/Cierre/Livewire/CierreForm.php",
    "resources/views/modules/admin/cierre/page.blade.php",
    "resources/views/modules/admin/cierre/form.blade.php"
];

// Carpetas vacías que deberían ser removidas para no dejar residuos
$carpetas_a_limpiar = [
    "app/Modules/Admin/Cierre/Livewire",
    "app/Modules/Admin/Cierre",
    "resources/views/modules/admin/cierre"
];

echo "🧹 Iniciando purga del submódulo de Cierre Diario...\n";

// 1. Remover archivos físicos
foreach ($archivos_a_borrar as $archivo) {
    if (file_exists($archivo)) {
        unlink($archivo);
        echo "  ❌ Eliminado archivo: {$archivo}\n";
    } else {
        echo "  ⚠️ No encontrado: {$archivo}\n";
    }
}

// 2. Remover carpetas si se quedaron vacías
foreach ($carpetas_a_limpiar as $carpeta) {
    if (is_dir($carpeta)) {
        // Solo elimina la carpeta si no tiene más archivos adentro
        $archivos_internos = array_diff(scandir($carpeta), ['.', '..']);
        if (empty($archivos_internos)) {
            rmdir($carpeta);
            echo "  🗑️ Carpeta removida: {$carpeta}\n";
        } else {
            echo "  🔔 Carpeta conservada (no está vacía): {$carpeta}\n";
        }
    }
}

echo "\n✨ Purga completada. Si corriste la migración previamente, recuerda ejecutar: php artisan migrate:rollback\n";