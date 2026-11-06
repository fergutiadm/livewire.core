<?php

namespace App\Modules\Ayuda\AyudaArticulo\Tables;

use App\Core\Tables\Actions\TableAction;
use App\Core\Tables\BaseTable;
use App\Core\Tables\Columns\ActionsColumn;
use App\Core\Tables\Columns\HtmlColumn;
use App\Core\Tables\Columns\TextColumn;
use App\Models\AyudaArticulo;
use Illuminate\Database\Eloquent\Builder;

class AyudaArticuloTableDefinition extends BaseTable
{
    public function __construct(array $parameters = [])
    {
        parent::__construct($parameters);
    }

    public function query(): Builder
    {
        return AyudaArticulo::query()
            ->when(
                filled($this->getParameter('categoriaId')),
                fn (Builder $query) => $query->where(
                    'categoria_id',
                    $this->getParameter('categoriaId')
                )
            )
            ->orderByRaw('orden_visual IS NULL ASC')
            ->orderBy('orden_visual')
            ->orderBy('id');
    }

    protected function searchable(): array
    {
        return [
            'titulo',
            'slug',
            'resumen',
            'contexto',
        ];
    }

    public function columns(): array
    {
        $titulo = (new HtmlColumn(
            label: 'Título',
            field: 'titulo',
        ))->formatStateUsing(function ($state, $articulo) {
            return '
                <div class="space-y-1">
                    <p class="font-semibold break-words">
                        ' . e($articulo->titulo ?? '—') . '
                    </p>
                    <p class="text-xs text-slate-500 break-words">
                        ' . e($articulo->slug ?? '—') . '
                    </p>
                </div>
            ';
        });

        $publicado = (new HtmlColumn(
            label: 'Publicado',
            field: 'publicado',
            extra: true,
        ))->formatStateUsing(function ($state, $articulo) {

            $texto = $state ? 'Sí' : 'No';

            $colorTexto = $state
                ? 'text-green-700'
                : 'text-red-700';

            $colorFondo = $state
                ? 'bg-green-100'
                : 'bg-red-100';

            return '
                <button
                    type="button"
                    wire:click="togglePublicado(' . (int) $articulo->id . ')"
                    title="Cambiar estado de publicación"
                    class="text-xs font-semibold px-2 py-1 rounded-full
                    ' . $colorTexto . ' ' . $colorFondo . '">
                    ' . $texto . '
                </button>
            ';
        });

        $acciones = (new ActionsColumn(
            label: 'Acciones',
            field: 'acciones',
        ))->actions([

            (new TableAction('Editar'))
                ->color('yellow')
                ->event('ayuda-articulo-editar')
                ->loading('Cargando artículo de ayuda...'),

            (new TableAction('Eliminar'))
                ->color('red')
                ->event('ayuda-articulo-eliminar')
                ->loading('Eliminando artículo de ayuda...'),

        ])->align('right');

        return [
            $titulo,

            new TextColumn(
                label: 'Resumen',
                field: 'resumen',
            ),

            new TextColumn(
                label: 'Contexto',
                field: 'contexto',
            ),

            $publicado,

            $acciones,
        ];
    }
}