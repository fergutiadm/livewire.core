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
            ->DeLocal($this->getParameter('localId'))
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
            label: 'Nombre',
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

        $fecha_inicio = (new HtmlColumn(
            label: 'Inicio',
            field: 'fecha_inicio',
        ))->formatStateUsing(function ($state, $periodo) {

            $fecha_inicio = $periodo->fecha_inicio ? format_fecha($periodo->fecha_inicio) : '';


            return '
                <p class="block text-sm text-slate-800 flex items-center flex-wrap-0">

                <span>' . e($fecha_inicio) . '</span>
            </p>
            ';
        });

        $fecha_fin = (new HtmlColumn(
            label: 'Fin',
            field: 'fecha_fin',
        ))->formatStateUsing(function ($state, $periodo) {

            $fecha_fin = $periodo->fecha_fin ? format_fecha($periodo->fecha_fin) : '';


            return '
                <p class="block text-sm text-slate-800 flex items-center flex-wrap-0">

                <span>' . e($fecha_fin) . '</span>
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

            $fecha_inicio,

            $fecha_fin,

            $acciones,
        ];
    }
}
