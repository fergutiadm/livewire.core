<?php

namespace App\Modules\Finanzas\ConceptoMonedaFondo\Tables;

use App\Core\Tables\Actions\TableAction;
use App\Core\Tables\BaseTable;
use App\Core\Tables\Columns\ActionsColumn;
use App\Core\Tables\Columns\HtmlColumn;
use App\Core\Tables\Columns\TextColumn;
use App\Models\ConceptoMonedaFondo;
use Illuminate\Database\Eloquent\Builder;

class ConceptoMonedaFondoTableDefinition extends BaseTable
{
    public function query(): Builder
    {
        $query = ConceptoMonedaFondo::query()
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
        ))->formatStateUsing(function ($state, $concepto) {
            return '
                <div class="space-y-1">
                    <p class="font-semibold break-words">
                        ' . e($concepto->nombre ?? '—') . '
                    </p>
                    <p class="text-xs text-slate-500 break-words">
                        ' . e($concepto->slug ?? '—') . '
                    </p>
                </div>
            ';
        });

        $activo = (new HtmlColumn(
            label: 'Activo',
            field: 'activo',
            extra: true,
        ))->formatStateUsing(function ($state, $concepto) {
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
                    wire:click="toggleActivo(' . e($concepto->id) . ')"
                    title="Clic para cambiar"
                    class="text-xs font-semibold px-2 py-0.5 rounded-full ml-2 flex-shrink-0
                    ' . $color_text . '
                    ' . $color_bg . '">
                    ' . e($texto) . '
                </button>
            ';
        });

        $entrada_salida = (new HtmlColumn(
            label: 'Movimiento de',
            field: 'entrada_salida',
            extra: true,
        ))->formatStateUsing(function ($state, $concepto) {
            $texto = $state ? 'Entrada' : 'Salida';

            $color_text = $state
                ? 'text-green-700'
                : 'text-red-700';

            $color_bg = $state
                ? 'bg-green-100'
                : 'bg-red-100';

            return '
                <button
                    type="button"
                    wire:click="toggleEntradaSalida(' . e($concepto->id) . ')"
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
                ->event('concepto-moneda-fondo-editar')
                ->loading('Cargando concepto...'),

            (new TableAction('Eliminar'))
                ->color('red')
                ->event('concepto-moneda-fondo-eliminar')
                ->loading('Cargando concepto...'),

        ])
        ->align('right');

        return [
            $nombre,

            new TextColumn(
                label: 'Tipo',
                field: 'tipo',
            ),

            $entrada_salida,

            $activo,

            $acciones,
        ];
    }
}