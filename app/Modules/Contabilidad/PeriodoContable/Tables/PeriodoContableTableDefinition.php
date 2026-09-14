<?php

namespace App\Modules\Contabilidad\PeriodoContable\Tables;

use App\Core\Tables\Actions\TableAction;
use App\Core\Tables\BaseTable;
use App\Core\Tables\Columns\ActionsColumn;
use App\Core\Tables\Columns\HtmlColumn;
use App\Core\Tables\Columns\TextColumn;
use App\Models\PeriodoContable;
use Illuminate\Database\Eloquent\Builder;

class PeriodoContableTableDefinition extends BaseTable
{
    public function query(): Builder
    {
        $query = PeriodoContable::query()
            ->when(
                filled($this->getParameter('localId')),
                fn ($q) => $q->where(
                    'local_id',
                    $this->getParameter('localId')
                )
            )
            ->orderByDesc('activo')
            ->orderByDesc('fecha_inicio');

        return $query;
    }

    protected function searchable(): array
    {
        return [
            'nombre',
            'fecha_inicio',
            'fecha_fin',
        ];
    }

    public function columns(): array
    {
        $nombre = (new HtmlColumn(
            label: 'Periodo',
            field: 'nombre',
        ))->formatStateUsing(function ($state, $periodo) {

            $activo = $periodo->activo
                ? '<i class="bi bi-check-circle-fill text-green-600"
                     title="Periodo activo"></i>'
                : '';

            $cerrado = $periodo->cerrado
                ? '<i class="bi bi-x-circle-fill text-red-500"
                     title="Periodo cerrado"></i>'
                : '';

            $nombre = $periodo->nombre
                ?: (
                    $periodo->fecha_inicio->format('d-m-Y')
                    . ' - ' .
                    $periodo->fecha_fin->format('d-m-Y')
                );

            return '
                <p class="block text-sm text-slate-800 flex items-center flex-nowrap gap-1">
                    ' . $activo . '
                    ' . $cerrado . '
                    <span class="text-sm text-slate-800 font-medium truncate">
                        ' . e($nombre) . '
                    </span>
                </p>
            ';

        });


        $acciones = (new ActionsColumn(
            label: 'Acciones',
            field: 'acciones',
        ))->actions([

            (new TableAction('Editar'))
                ->color('yellow')
                ->event('periodo-contable-editar')
                ->loading('Cargando periodo contable...'),

            (new TableAction('Eliminar'))
                ->color('red')
                ->event('periodo-contable-eliminar')
                ->loading('Cargando periodo contable...'),

        ])
        ->align('right');

        return [
            $nombre,

            new TextColumn(
                label: 'Inicio',
                field: 'fecha_inicio',
            ),
            new TextColumn(
                label: 'Fin',
                field: 'fecha_fin',
            ),

            $acciones,
        ];
    }
}
