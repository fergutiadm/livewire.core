<?php

namespace App\Modules\Inventario\Producto\Tables;

use App\Core\Tables\BaseTable;
use App\Core\Tables\Columns\TextColumn;
use App\Core\Tables\Columns\ActionsColumn;
use App\Core\Tables\Actions\TableAction;
use App\Core\Tables\Columns\HtmlColumn;
use App\Models\Producto;
use Illuminate\Database\Eloquent\Builder;

class ProductoTableDefinition extends BaseTable
{
    public ?int $localId = null;
    public ?int $categoriaId = null;

    public function __construct(array $params = [])
    {
        $this->localId = $params['localId'] ?? null;
        $this->categoriaId = $params['categoriaId'] ?? null;
    }

    public function query(): Builder
    {
        $query = Producto::query()

            ->when(
                filled($this->getParameter('localId')),
                fn ($q) => $q->where(
                    'local_id',
                    $this->getParameter('localId')
                )
            )

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
            'nombre',
            'codigo',
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
                ->event('producto-editar')
                ->loading('Cargando producto...'),

            (new TableAction('Eliminar'))
                ->color('red')
                ->event('producto-eliminar')
                ->loading('Eliminando producto...'),

            (new TableAction('Atributos'))
                ->color('magenta')
                ->event('producto-attributes-editar'),

        ])
        ->align('right');

        $codigo = (new HtmlColumn(
            label: 'Código',
            field: 'codigo',
            extra: true,
            extra_msj: 'Clic para copiar',
        ))->formatStateUsing(function ($state, $producto) {

            $color_text = 'text-green-700';

            $color_bg = 'bg-green-100';

            return '
                <button
                    type="button"
                    @click="copiarAlPortapapeles(\'' . e($state) . '\')"
                    title="Clic para copiar"
                    class="accion-copiar text-xs font-semibold px-2 py-0.5 rounded-full ml-2 flex-shrink-0
                    ' . $color_text . '
                    ' . $color_bg . '">
                    ' . e($state) . '
                </button>
            ';
        });

        return [
            new TextColumn(
                label: 'Nombre',
                field: 'nombre',
            ),

            $codigo,

            new TextColumn(
                label: 'Costo',
                field: 'costo',
            ),

            new TextColumn(
                label: 'Precio',
                field: 'precio',
            ),

            $acciones,
        ];
    }
}
