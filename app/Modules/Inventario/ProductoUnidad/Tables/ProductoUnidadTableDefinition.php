<?php

namespace App\Modules\Inventario\ProductoUnidad\Tables;


use App\Core\Tables\Actions\TableAction;
use App\Core\Tables\BaseTable;
use App\Core\Tables\Columns\TextColumn;
use App\Core\Tables\Columns\ActionsColumn;
use App\Core\Tables\Columns\HtmlColumn;
use Illuminate\Database\Eloquent\Builder;
use App\Models\ProductoUnidad;

class ProductoUnidadTableDefinition extends BaseTable
{
    public ?int $productoId = null;
    public ?int $unidadMedidaId = null;

    public function __construct(array $params = [])
    {
        $this->productoId = $params['productoId'] ?? null;
        $this->unidadMedidaId = $params['unidadMedidaId'] ?? null;
    }

    public function query(): Builder
    {
        $query = Producto::query()

            ->when(
                filled($this->getParameter('productoId')),
                fn ($q) => $q->where(
                    'producto_id',
                    $this->getParameter('productoId')
                )
            )

            ->when(
                filled($this->getParameter('unidadMedidaId')),
                fn ($q) => $q->where(
                    'unidad_medida_id',
                    $this->getParameter('unidadMedidaId')
                )
            );

            // $sql = vsprintf(
            //     str_replace('?', "'%s'", $query->toSql()),
            //     $query->getBindings()
            // );
            // \Log::info('ProductoUnidadTableDefinition->query', [
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
            'factor_a_base',
            'permite_fraccion',
        ];
    }

    public function columns(): array
    {
        $activo = (new HtmlColumn(
            label: 'Activo',
            field: 'activo',
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
                ->event('producto-unidad-editar')
                ->loading('Cargando producto-unidad...'),

            (new TableAction('Eliminar'))
                ->color('red')
                ->event('producto-unidad-eliminar')
                ->loading('Eliminando producto-unidad...'),

        ])
        ->align('right');

        return [
            new TextColumn(
                label: 'Producto',
                field: 'producto_id',
            ),

            new TextColumn(
                label: 'UnidadMedida',
                field: 'unidad_medida_id',
            ),

            new TextColumn(
                label: 'Factor-Base',
                field: 'factor_a_base',
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