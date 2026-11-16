<?php

namespace App\Modules\Configuracion\Provincia\Tables;

use App\Core\Tables\Actions\TableAction;
use App\Core\Tables\BaseTable;
use App\Core\Tables\Columns\ActionsColumn;
use App\Core\Tables\Columns\HtmlColumn;
use App\Core\Tables\Columns\TextColumn;
use App\Models\Provincia;
use App\Modules\Configuracion\SetupConfig\Services\SetupConfigService;
use Illuminate\Database\Eloquent\Builder;

class ProvinciaTableDefinition extends BaseTable
{
    public function query(): Builder
    {
        $query = Provincia::query()
            ->orderBy('orden')
            ->orderBy('nombre');

        return $query;
    }

    protected function searchable(): array
    {
        return [
            'nombre',
            'slug',
        ];
    }

    public function columns(): array
    {
        $nombre = (new HtmlColumn(
            label: 'Nombre',
            field: 'nombre',
        ))->formatStateUsing(function ($state, $provincia) {
            return '
                <div class="space-y-1">
                    <p class="font-semibold break-words">
                        ' . e($provincia->nombre ?? '—') . '
                    </p>
                    <p class="text-xs text-slate-500 break-words">
                        ' . e($provincia->slug ?? '—') . '
                    </p>
                </div>
            ';
        });

        $activa = (new HtmlColumn(
            label: 'Activa',
            field: 'activa',
            extra: true,
        ))->formatStateUsing(function ($state, $provincia) {
            $texto = $state ? 'Sí' : 'NO';

            $color_text = $state
                ? 'text-green-700'
                : 'text-red-700';

            $color_bg = $state
                ? 'bg-green-100'
                : 'bg-red-100';

            return '
                <button
                    type="button"
                    wire:click="toggleActiva(' . e($provincia->id) . ')"
                    title="Clic para cambiar"
                    class="text-xs font-semibold px-2 py-0.5 rounded-full ml-2 flex-shrink-0
                    ' . $color_text . '
                    ' . $color_bg . '">
                    ' . e($texto) . '
                </button>
            ';
        });

        $porDefecto = (new HtmlColumn(
            label: 'Por Defecto',
            field: 'slug',
            extra: true,
        ))->formatStateUsing(function ($state, $provincia) {
            $provincia_default = app(SetupConfigService::class)->get('provincia_default', '');
            $texto = ($state === $provincia_default) ? 'Sí' : 'NO';

            $color_text = ($state === $provincia_default)
                ? 'text-green-700'
                : 'text-emerald-700';

            $color_bg = ($state === $provincia_default)
                ? 'bg-emerald-100'
                : 'bg-orange-100';

            return '
                <button
                    type="button"
                    wire:click="togglePorDefecto(' . e($provincia->id) . ')"
                    title="Clic para cambiar"
                    class="text-xs font-semibold px-2 py-0.5 rounded-full ml-2 flex-shrink-0
                    ' . $color_text . '
                    ' . $color_bg . '">
                    ' . e($texto) . '
                </button>
            ';
        });

        $acciones = (new ActionsColumn(
            label: 'Acciones',
            field: 'acciones',
        ))->actions([

            (new TableAction('Editar'))
                ->color('yellow')
                ->event('provincia-editar')
                ->loading('Cargando provincia...'),

            (new TableAction('Eliminar'))
                ->color('red')
                ->event('provincia-eliminar')
                ->loading('Cargando provincia...'),

        ])
        ->align('right');

        return [
            $nombre,

            $activa,

            $porDefecto,

            $acciones,
        ];
    }
}