<?php

namespace App\Modules\Inventario\TipoLocal\Tables;

use App\Core\Tables\Actions\TableAction;
use App\Core\Tables\BaseTable;
use App\Core\Tables\Columns\ActionsColumn;
use App\Core\Tables\Columns\HtmlColumn;
use App\Core\Tables\Columns\TextColumn;
use App\Models\TipoLocal;
use Illuminate\Database\Eloquent\Builder;

class TipoLocalTableDefinition extends BaseTable
{
    public function query(): Builder
    {
        $query = TipoLocal::query()
            ->orderBy('codigo');

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
        $activo = (new HtmlColumn(
            label: 'Activo',
            field: 'activo',
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
                    wire:click="toggleActivo(' . e($moneda->id) . ')"
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
            new TextColumn(
                label: 'Código',
                field: 'codigo',
            ),

            new TextColumn(
                label: 'Nombre',
                field: 'nombre',
            ),

            new TextColumn(
                label: 'Descripción',
                field: 'descripcion',
            ),

            $activo,

            $acciones,
        ];
    }
}