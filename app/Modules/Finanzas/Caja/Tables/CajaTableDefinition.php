<?php

namespace App\Modules\Finanzas\Caja\Tables;

use App\Core\Tables\Actions\TableAction;
use App\Core\Tables\BaseTable;
use App\Core\Tables\Columns\ActionsColumn;
use App\Core\Tables\Columns\HtmlColumn;
use App\Core\Tables\Columns\TextColumn;
use App\Models\Caja;
use Illuminate\Database\Eloquent\Builder;

class CajaTableDefinition extends BaseTable
{
    public function query(): Builder
    {
        $query = Caja::query()

            ->when(
                filled($this->getParameter('localId')),
                fn ($q) => $q->where(
                    'local_id',
                        $this->getParameter('localId')
                )
            );

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
        $activa = (new HtmlColumn(
            label: 'Activa',
            field: 'activa',
            extra: true,
        ))->formatStateUsing(function ($state, $caja) {
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
                    wire:click="toggleActiva(' . e($caja->id) . ')"
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
                ->event('caja-editar')
                ->loading('Cargando caja...'),

            (new TableAction('Eliminar'))
                ->color('red')
                ->event('caja-eliminar')
                ->loading('Cargando caja...'),

        ])
        ->align('right');

        return [
            new TextColumn(
                label: 'Nombre',
                field: 'nombre',
            ),

            new TextColumn(
                label: 'Código',
                field: 'codigo',
            ),

            $activa,

            $acciones,
        ];
    }
}
