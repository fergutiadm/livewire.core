<?php

namespace App\Modules\Ayuda\AyudaCategoria\Tables;

use App\Core\Tables\Actions\TableAction;
use App\Core\Tables\BaseTable;
use App\Core\Tables\Columns\ActionsColumn;
use App\Core\Tables\Columns\HtmlColumn;
use App\Core\Tables\Columns\TextColumn;
use App\Models\AyudaCategoria;
use Illuminate\Database\Eloquent\Builder;

class AyudaCategoriaTableDefinition extends BaseTable
{
    public function query(): Builder
    {
        $query = AyudaCategoria::query()
            ->orderBy('orden_visual');

        return $query;
    }

    public function columns(): array
    {
        $nombre = (new HtmlColumn(
            label: 'Nombre',
            field: 'nombre',
        ))->formatStateUsing(function ($state, $categoria) {
            return '
                <div class="space-y-1">
                    <p class="flex items-start">
                        <span class="break-words font-semibold">'
                            . e($categoria->nombre ?? '—') .
                        '</span>
                    </p>

                    <p class="flex items-start gap-1 text-xs">
                        <span class="shrink-0 font-semibold break-words text-slate-500">'
                            . e($categoria->slug ?? '—') .
                        '</span>
                    </p>
                </div>
            ';
        });

        $activa = (new HtmlColumn(
            label: 'Activa',
            field: 'activa',
            extra: true,
        ))->formatStateUsing(function ($state, $categoria) {
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
                    wire:click="toggleActiva(' . e($categoria->id) . ')"
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
                ->event('ayuda-categoria-editar')
                ->loading('Cargando categoría...'),

            (new TableAction('Eliminar'))
                ->color('red')
                ->event('ayuda-categoria-eliminar')
                ->loading('Eliminando categoría...'),
        ])
        ->align('right');

        return [
            $nombre,

            new TextColumn(
                label: 'Descripcion',
                field: 'descripcion',
            ),

            $activa,

            $acciones,
        ];
    }
}