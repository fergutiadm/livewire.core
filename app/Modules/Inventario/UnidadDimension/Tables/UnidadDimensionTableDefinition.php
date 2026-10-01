<?php

namespace App\Modules\Inventario\UnidadDimension\Tables;

use App\Core\Tables\Actions\TableAction;
use App\Core\Tables\BaseTable;
use App\Core\Tables\Columns\TextColumn;
use App\Core\Tables\Columns\ActionsColumn;
use App\Core\Tables\Columns\HtmlColumn;
use Illuminate\Database\Eloquent\Builder;
use App\Models\UnidadDimension;

class UnidadDimensionTableDefinition extends BaseTable
{

    public function query(): Builder
    {
        $query = UnidadDimension::query()

            ->when(
                filled($this->getParameter('codigo')),
                fn ($q) => $q->where(
                    'codigo',
                    $this->getParameter('codigo')
                )
            );

            // $sql = vsprintf(
            //     str_replace('?', "'%s'", $query->toSql()),
            //     $query->getBindings()
            // );
            // \Log::info('UnidadDimensionTableDefinition->query', [
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
        ))->formatStateUsing(function ($state, $unidad_dimension) {
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
                    wire:click="toggleActiva(' . e($unidad_dimension->id) . ')"
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
                ->event('unidad-dimension-editar')
                ->loading('Cargando unidad-dimension...'),

            (new TableAction('Eliminar'))
                ->color('red')
                ->event('unidad-dimension-eliminar')
                ->loading('Eliminando unidad-dimension...'),

        ])
        ->align('right');

        return [
            new TextColumn(
                label: 'Codigo',
                field: 'codigo',
            ),

            new TextColumn(
                label: 'Nombre',
                field: 'nombre',
            ),

            new TextColumn(
                label: 'Descripcion',
                field: 'descripcion',
            ),

            $activo,

            $acciones,
        ];
    }
}