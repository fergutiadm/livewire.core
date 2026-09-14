<?php

namespace App\Modules\Finanzas\Moneda\Tables;

use App\Core\Tables\Actions\TableAction;
use App\Core\Tables\BaseTable;
use App\Core\Tables\Columns\ActionsColumn;
use App\Core\Tables\Columns\HtmlColumn;
use App\Core\Tables\Columns\TextColumn;
use App\Models\Moneda;
use Illuminate\Database\Eloquent\Builder;

class MonedaTableDefinition extends BaseTable
{
    public function query(): Builder
    {
        $query = Moneda::query();

        return $query;
    }

    protected function searchable(): array
    {
        return [
            'nombre',
            'codigo',
        ];
    }

    public function columns(): array
    {
        $nombre = (new HtmlColumn(
            label: 'Nombre',
            field: 'nombre',
        ))->formatStateUsing(function ($state, $moneda) {

            $nombre = $moneda->nombre ?? '';
            $simbolo = $moneda->simbolo ?? '';

            $color_text = $moneda->color_text ?? '';
            $color_bg = $moneda->color_bg ?? '';


            return '
                <p class="block text-sm text-slate-800 flex items-center flex-wrap-0">

                <span class="truncate">' . e($nombre) . '</span>

                <span
                        class="text-xs font-semibold px-2 py-0.5 rounded-full ml-2 flex-shrink-0
                        ' . e($color_text) . '
                        ' . e($color_bg)  . '">
                    ' . e($simbolo) .
                '</span>

            </p>
            ';
        });

        $activa = (new HtmlColumn(
            label: 'Activa',
            field: 'activa',
            extra: true,
        ))->formatStateUsing(function ($state, $moneda) {
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
                    wire:click="toggleActiva(' . e($moneda->id) . ')"
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
                ->event('moneda-editar')
                ->loading('Cargando moneda...'),

            (new TableAction('Eliminar'))
                ->color('red')
                ->event('moneda-eliminar')
                ->loading('Cargando moneda...'),

        ])
        ->align('right');

        return [
            $nombre,

            new TextColumn(
                label: 'Código',
                field: 'codigo',
            ),

            new TextColumn(
                label: 'TasaCambio',
                field: 'tasa_cambio',
            ),

            $activa,

            $acciones,
        ];
    }
}
