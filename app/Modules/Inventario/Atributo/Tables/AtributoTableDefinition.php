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
        return Atributo::query()
            ->withCount([
                'valores as valores_activos_count' => function ($query) {
                    $query->where('activo', true);
                },
            ])
            ->orderBy('orden_visual');
    }

    protected function searchable(): array
    {
        return [
            'nombre',
            'codigo',
            'descripcion',
        ];
    }

    public function columns(): array
    {
        $nombre = (new HtmlColumn(
            label: 'Nombre',
            field: 'nombre',
        ))->formatStateUsing(function ($state, $atributo) {
            $nombre = $atributo->nombre ?? '';
            $ordenVisual = $atributo->orden_visual ?? '';

            $colorText = $atributo->color_text
                ?? 'text-white';

            $colorBg = $atributo->color_bg
                ?? 'bg-indigo-500';

            return '
                <p class="
                    block
                    text-sm
                    text-slate-800
                    flex
                    items-center
                    flex-wrap-0
                ">
                    <span class="truncate">
                        ' . e($nombre) . '
                    </span>

                    <span
                        class="
                            text-xs
                            font-semibold
                            px-2
                            py-0.5
                            rounded-full
                            ml-2
                            flex-shrink-0
                            ' . e($colorText) . '
                            ' . e($colorBg) . '
                        "
                    >
                        ' . e($ordenVisual) . '
                    </span>
                </p>
            ';
        });

        $activo = (new HtmlColumn(
            label: 'Activo',
            field: 'activo',
            extra: true,
        ))->formatStateUsing(function ($state, $atributo) {
            $texto = $state ? 'Sí' : 'NO';

            $colorText = $state
                ? 'text-green-700'
                : 'text-red-700';

            $colorBg = $state
                ? 'bg-green-100'
                : 'bg-red-100';

            return '
                <button
                    type="button"
                    wire:click="toggleActivo(' .
                        e($atributo->id) .
                    ')"
                    title="Clic para cambiar"
                    class="
                        text-xs
                        font-semibold
                        px-2
                        py-0.5
                        rounded-full
                        ml-2
                        flex-shrink-0
                        ' . $colorText . '
                        ' . $colorBg . '
                    "
                >
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
                ->event('atributo-editar')
                ->loading('Cargando atributo...'),

            (new TableAction('Eliminar'))
                ->color('red')
                ->event('atributo-eliminar')
                ->loading('Desactivando atributo...'),
        ])
        ->align('right');

        return [
            $nombre,

            new TextColumn(
                label: 'Código',
                field: 'codigo',
            ),

            new TextColumn(
                label: 'Descripción',
                field: 'descripcion',
            ),

            new TextColumn(
                label: 'Valores',
                field: 'valores_activos_count',
            ),

            $activo,

            $acciones,
        ];
    }
}