<?php

namespace App\Modules\Finanzas\TarjetaMagnetica\Tables;

use App\Core\Tables\Actions\TableAction;
use App\Core\Tables\BaseTable;
use App\Core\Tables\Columns\ActionsColumn;
use App\Core\Tables\Columns\HtmlColumn;
use App\Core\Tables\Columns\TextColumn;
use App\Models\TarjetaMagnetica;
use Illuminate\Database\Eloquent\Builder;

class TarjetaMagneticaTableDefinition extends BaseTable
{
    public function query(): Builder
    {
        $query = TarjetaMagnetica::query();

        return $query;
    }

    protected function searchable(): array
    {
        return [
            'numero',
            'propietario',
        ];
    }

    public function columns(): array
    {
        $numero = (new HtmlColumn(
            label: 'Número',
            field: 'numero',
        ))->formatStateUsing(function ($state, $tarjeta_magnetica) {

            $numero = $tarjeta_magnetica->numero ?? '';
            $monedaCodigo = $tarjeta_magnetica->monedaCodigo ?? '';

            $color_text = $tarjeta_magnetica->color_text ?? '';
            $color_bg = $tarjeta_magnetica->color_bg ?? '';


            return '
                <p class="block text-sm text-slate-800 flex items-center flex-wrap-0">

                <span class="truncate">' . e($numero) . '</span>

                <span
                        class="text-xs font-semibold px-2 py-0.5 rounded-full ml-2 flex-shrink-0
                        ' . e($color_text) . '
                        ' . e($color_bg)  . '">
                    ' . e($monedaCodigo) .
                '</span>

            </p>
            ';
        });


        $acciones = (new ActionsColumn(
            label: 'Acciones',
            field: 'acciones',
        ))->actions([

            (new TableAction('Editar'))
                ->color('yellow')
                ->event('tarjeta-magnetica-editar')
                ->loading('Cargando tarjeta magnética'),

            (new TableAction('Eliminar'))
                ->color('red')
                ->event('tarjeta-magnetica-eliminar')
                ->loading('Eliminando tarjeta magnética'),

        ])
        ->align('right');

        return [
            $numero,

            new TextColumn(
                label: 'Propietario',
                field: 'propietario',
            ),

            $acciones,
        ];
    }
}