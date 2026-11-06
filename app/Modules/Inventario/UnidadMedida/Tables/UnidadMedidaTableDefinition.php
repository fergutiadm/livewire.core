<?php

namespace App\Modules\Inventario\UnidadMedida\Tables;

use App\Core\Tables\Actions\TableAction;
use App\Core\Tables\BaseTable;
use App\Core\Tables\Columns\TextColumn;
use App\Core\Tables\Columns\ActionsColumn;
use App\Core\Tables\Columns\HtmlColumn;
use Illuminate\Database\Eloquent\Builder;
use App\Models\UnidadMedida;
use Illuminate\Support\Facades\Log;

class UnidadMedidaTableDefinition extends BaseTable
{
    public ?int $unidadDimensionId = null;

    public function __construct(array $params = [])
    {
        $this->unidadDimensionId = $params['unidadDimensionId'] ?? null;
    }

    public function query(): Builder
    {
        $query = UnidadMedida::query()

            ->whereHas('dimension', function ($query) {
                $query->where('activa', true);
            })

            ->when(
                filled($this->getParameter('unidadDimensionId')),
                fn ($q) => $q->where(
                    'unidad_dimension_id',
                    $this->getParameter('unidadDimensionId')
                )
            );

            // $sql = vsprintf(
            //     str_replace('?', "'%s'", $query->toSql()),
            //     $query->getBindings()
            // );
            // \Log::info('UnidadMedidaTableDefinition->query', [
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
            // Log::info('UnidadMedidaTableDefinition', [
            //     'unidadDimensionId' => $this->getParameter('unidadDimensionId'),
            //     'sql' => $query->toSql(),
            //     'bindings' => $query->getBindings(),
            // ]);
        return $query;
    }

    protected function searchable(): array
    {
        return [
            'nombre',
            'codigo',
            'abreviatura',
            'tipo',
        ];
    }

    public function columns(): array
    {
        $unidadDimension = (new HtmlColumn(
            label: 'Dimensión',
            field: 'unidad_dimension',
        ))->formatStateUsing(function ($state, $unidad_medida) {
            $unidad_dimension = $unidad_medida->dimension;
            return $unidad_dimension ? '
                <div class="space-y-1">
                    <p class="flex items-start">
                        <span class="break-words font-semibold">'
                            . e($unidad_dimension->nombre ?? '—') .
                        '</span>
                    </p>

                    <p class="flex items-start gap-1 text-xs">
                        <span class="shrink-0 font-semibold text-slate-800">
                            Código:
                        </span>
                        <span class="break-words text-slate-500">'
                            . e($unidad_dimension->codigo ?? '—') .
                        '</span>
                    </p>
                </div>
            ' : '---';
        });

        $activo = (new HtmlColumn(
            label: 'Activa',
            field: 'activa',
            extra: true,
        ))->formatStateUsing(function ($state, $producto_unidad) {
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
                    wire:click="toggleActiva(' . e($producto_unidad->id) . ')"
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
                ->event('unidad-medida-editar')
                ->loading('Cargando unidad de medida...'),

            (new TableAction('Eliminar'))
                ->color('red')
                ->event('unidad-medida-eliminar')
                ->loading('Eliminando unidad de medida...'),

        ])
        ->align('right');

        return [
             $unidadDimension,

            new TextColumn(
                label: 'Codigo',
                field: 'codigo',
            ),

            new TextColumn(
                label: 'Nombre',
                field: 'nombre',
            ),

            new TextColumn(
                label: 'Abreviatura',
                field: 'abreviatura',
            ),

            new TextColumn(
                label: 'Tipo',
                field: 'tipo',
            ),

            new TextColumn(
                label: 'Factor-Base',
                field: 'factor_a_base',
            ),

            new TextColumn(
                label: 'EsBaseDimension',
                field: 'es_base_dimension',
            ),

            new TextColumn(
                label: 'PermiteFraccion',
                field: 'permite_fraccion',
            ),

            $activo,

            $acciones,
        ];
    }
}