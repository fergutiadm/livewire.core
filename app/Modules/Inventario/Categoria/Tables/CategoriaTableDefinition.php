<?php

namespace App\Modules\Inventario\Categoria\Tables;

use App\Core\Tables\BaseTable;
use App\Core\Tables\Columns\TextColumn;
use App\Core\Tables\Columns\ActionsColumn;
use App\Core\Tables\Actions\TableAction;
use App\Core\Tables\Columns\HtmlColumn;
use App\Models\Categoria;
use Illuminate\Database\Eloquent\Builder;

class CategoriaTableDefinition extends BaseTable
{
    public function query(): Builder
    {
        $query = Categoria::query()
            ->when(
                filled($this->getParameter('localId')),
                fn ($q) => $q->where(
                    'local_id',
                    $this->getParameter('localId')
                )
            )
            ->orderBy('orden_visual');

        return $query;
    }

    public function columns(): array
    {
        $nombre = (new HtmlColumn(
            label: 'Nombre',
            field: 'nombre',
        ))->formatStateUsing(function ($state, $categoria) {

            $icono = $categoria->icono ?? '';
            $icono_secundario = $categoria->icono_secundario ?? '';

            $class_colores = $categoria->color_bg ?? '';
            $class_colores .= ' ' . ($categoria->color_text ?? '');

            return '
                <div class="flex items-center gap-2">

                    <span class="text-xs font-semibold text-white bg-indigo-500 px-2 py-0.5 rounded-full">
                        ' . e($categoria->orden_visual) . '
                    </span>

                    <span class="truncate px-2 py-0.5 rounded {{ e($class_colores) }}">
                        ' . e($categoria->nombre) . '
                    </span>

                    ' . ($icono ? '
                        <span class="text-xs ' . e($class_colores) . ' px-2 py-0.5 rounded-full">
                            <i class="bi ' . e($icono) . '"></i>
                        </span>
                    ' : '') . '

                    ' . ($icono_secundario ? '
                        <span class="text-xs ' . e($class_colores) . ' px-2 py-0.5 rounded-full">
                            <i class="bi ' . e($icono_secundario) . '"></i>
                        </span>
                    ' : '') . '

                </div>
            ';
        });

        $acciones = (new ActionsColumn(
            label: 'Acciones',
            field: 'acciones',
        ))->actions([
            (new TableAction('Editar'))
                ->color('yellow')
                ->event('categoria-editar')
                ->loading('Cargando categoría...'),

            (new TableAction('Eliminar'))
                ->color('red')
                ->event('categoria-eliminar')
                ->loading('Eliminando categoría...'),

            (new TableAction('Atributos'))
                ->color('magenta')
                ->event('categoria-attributes-editar'),
        ])
        ->align('right');

        return [
            $nombre,

            new TextColumn(
                label: 'Descripcion',
                field: 'descripcion',
            ),

            new TextColumn(
                label: '% Descuento',
                field: 'porciento_descuento',
            ),

            $acciones,
        ];
    }
}
