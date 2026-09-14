<?php

namespace App\Modules\Inventario\Atributo\Tables;

use App\Core\Tables\Actions\TableAction;
use App\Core\Tables\BaseTable;
use App\Core\Tables\Columns\ActionsColumn;
use App\Core\Tables\Columns\HtmlColumn;
use App\Core\Tables\Columns\TextColumn;
use App\Models\Atributo;
use Illuminate\Database\Eloquent\Builder;

class AtributoTableDefinition extends BaseTable
{
    public function query(): Builder
    {
        $query = Atributo::query()
            ->orderBy('orden_visual');

        return $query;
    }

    protected function searchable(): array
    {
        return [
            'nombre',
            'descripcion',
        ];
    }

    public function columns(): array
    {
        $acciones = (new ActionsColumn(
            label: 'Acciones',
            field: 'acciones',
        ))->actions([

            (new TableAction('Editar'))
                ->color('yellow')
                ->event('atributo-editar')
                ->loading('Cargando atributo...'),

            (new TableAction('Eliminar'))
                ->color('red')
                ->event('atributo-eliminar')
                ->loading('Eliminando atributo...'),

        ])
        ->align('right');

        $nombre = (new HtmlColumn(
            label: 'Nombre',
            field: 'nombre',
        ))->formatStateUsing(function ($state, $atributo) {

            $nombre = $atributo->nombre ?? '';
            $orden_visual = $atributo->orden_visual ?? '';

            $color_text = $atributo->color_text ?? 'text-white';
            $color_bg = $atributo->color_bg ?? 'bg-indigo-500';


            return '
                <p class="block text-sm text-slate-800 flex items-center flex-wrap-0">

                <span class="truncate">' . e($nombre) . '</span>

                <span
                        class="text-xs font-semibold px-2 py-0.5 rounded-full ml-2 flex-shrink-0
                        ' . e($color_text) . '
                        ' . e($color_bg)  . '">
                    ' . e($orden_visual) .
                '</span>

            </p>
            ';
        });

        return [
            $nombre,

            new TextColumn(
                label: 'Descripción',
                field: 'descripcion',
            ),

            $acciones,
        ];
    }
}
