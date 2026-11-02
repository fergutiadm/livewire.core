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
    public ?int $categoriaId = null;

    public function __construct(array $params = [])
    {
        $this->categoriaId = $params['categoriaId'] ?? null;
    }

    public function query(): Builder
    {
        $query = AyudaArticulo::query()

            ->when(
                filled($this->getParameter('categoriaId')),
                fn ($q) => $q->where(
                    'categoria_id',
                    $this->getParameter('categoriaId')
                )
            );

            // $sql = vsprintf(
            //     str_replace('?', "'%s'", $query->toSql()),
            //     $query->getBindings()
            // );
            // \Log::info('ProductoTableDefinition->query', [
            //     'sql' => $sql,
            //     '$query->toSql()' => $query->toSql(),
            //     ' $query->getBindings()' => $query->getBindings(),
            //     '$query->count()' => $query->count()]
            // );
            // dd(
            //     $sql,
            //     $query->toSql(),
            //     $query->getBindings(),
            //     $query->count()
            // );

        return $query;
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
            label: 'Titulo',
            field: 'titulo',
        ))->formatStateUsing(function ($state, $categoria) {
            return '
                <div class="space-y-1">
                    <p class="flex items-start">
                        <span class="break-words font-semibold">'
                            . e($categoria->titulo ?? '—') .
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

        $publicado = (new HtmlColumn(
            label: 'Publicado',
            field: 'publicado',
            extra: true,
        ))->formatStateUsing(function ($state, $ayudaArticulo) {
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
                    wire:click="togglePublicado(' . e($ayudaArticulo->id) . ')"
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
                ->event('ayuda-articulo-editar')
                ->loading('Cargando artículo de ayuda...'),

            (new TableAction('Eliminar'))
                ->color('red')
                ->event('ayuda-articulo-eliminar')
                ->loading('Eliminando artículo de ayuda...'),
        ])
        ->align('right');

        return [
            $titulo,

            new TextColumn(
                label: 'Contenino',
                field: 'contenido',
            ),

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