$ErrorActionPreference = "Stop"

$base = "C:\laragon\www\laravel\livewire"

$entidades = @(
    "categorias",
    "atributos",
    "clientes",
    "locales",
    "periodos-contables",
    "tarjetas-magneticas",
    "usuarios"
)

# ============================================================
# BACKUP
# ============================================================

$backup = Join-Path $base "storage\app\legacy-ui-backup-$(Get-Date -Format 'yyyyMMdd-HHmmss')"

New-Item -ItemType Directory -Path $backup -Force | Out-Null

Write-Host ""
Write-Host "============================================" -ForegroundColor Cyan
Write-Host " MIGRACION UI LEGACY" -ForegroundColor Cyan
Write-Host "============================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Backup: $backup" -ForegroundColor Yellow
Write-Host ""

foreach ($entidad in $entidades) {

    $main = Join-Path $base "resources\views\livewire\admin\$entidad.blade.php"
    $table = Join-Path $base "resources\views\livewire\admin\tabla-$entidad.blade.php"

    foreach ($file in @($main, $table)) {

        if (Test-Path $file) {

            Copy-Item $file $backup -Force

            Write-Host "Backup:" (Split-Path $file -Leaf) -ForegroundColor DarkGray
        }
        else {

            Write-Host "NO EXISTE:" $file -ForegroundColor Red
        }
    }
}

# ============================================================
# REEMPLAZOS GENERALES
# ============================================================

$replacements = [ordered]@{

    # --------------------------------------------------------
    # FORMULARIOS
    # --------------------------------------------------------

    'panel-form-tab-group-2' = 'panel-form-grid-2'
    'panel-form-tab-group-3' = 'panel-form-grid-3'
    'panel-form-tab-group-4' = 'panel-form-grid-4'

    # --------------------------------------------------------
    # LAYOUT
    # --------------------------------------------------------

    'panel-layout-3' = 'panel-layout'

    # --------------------------------------------------------
    # BOTONES
    # --------------------------------------------------------

    'btn-primary-1' = 'btn-primary'
    'btn-danger-1'  = 'btn-danger'
    'btn-cancel'    = 'btn-secondary'

    # btn-warning no tiene equivalente exacto actualmente.
    # Lo dejamos para revisión manual.
}

# ============================================================
# PROCESAR LOS 14 BLADES
# ============================================================

foreach ($entidad in $entidades) {

    $files = @(
        Join-Path $base "resources\views\livewire\admin\$entidad.blade.php"
        Join-Path $base "resources\views\livewire\admin\tabla-$entidad.blade.php"
    )

    foreach ($file in $files) {

        if (!(Test-Path $file)) {
            continue
        }

        $content = Get-Content $file -Raw
        $original = $content

        foreach ($old in $replacements.Keys) {

            $new = $replacements[$old]

            $content = $content.Replace($old, $new)
        }

        if ($content -ne $original) {

            Set-Content `
                -Path $file `
                -Value $content `
                -Encoding UTF8

            Write-Host ""
            Write-Host "ACTUALIZADO:" (Split-Path $file -Leaf) -ForegroundColor Green
        }
        else {

            Write-Host ""
            Write-Host "SIN CAMBIOS:" (Split-Path $file -Leaf) -ForegroundColor DarkGray
        }
    }
}

Write-Host ""
Write-Host "============================================" -ForegroundColor Cyan
Write-Host " MIGRACION TERMINADA" -ForegroundColor Green
Write-Host "============================================" -ForegroundColor Cyan
Write-Host ""

Write-Host "IMPORTANTE:" -ForegroundColor Yellow
Write-Host "No se modificaron btn-warning, table-container,"
Write-Host "table-header, panel-left ni panel-right."
Write-Host "Esas clases requieren revisar su estructura antes"
Write-Host "de hacer un reemplazo automático."
Write-Host ""

Write-Host "Backup disponible en:" -ForegroundColor Yellow
Write-Host $backup
Write-Host ""