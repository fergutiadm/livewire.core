<?php

namespace App\Modules\Inventario\Local\Tables;

use App\Core\Tables\Actions\TableAction;
use App\Core\Tables\BaseTable;
use App\Core\Tables\Columns\ActionsColumn;
use App\Core\Tables\Columns\HtmlColumn;
use App\Core\Tables\Columns\TextColumn;
use App\Models\Local;
use Illuminate\Database\Eloquent\Builder;

class LocalTableDefinition extends BaseTable
{
    public function query(): Builder
    {
        $query = Local::query()
            ->orderByDesc('es_principal');

        return $query;
    }

    protected function searchable(): array
    {
        return [
            'nombre',
            'descripcion',
        ];
    }

    public function columns(): array
    {
        $nombre = (new HtmlColumn(
            label: 'Nombre',
            field: 'nombre',
        ))->formatStateUsing(function ($state, $local) {

            $es_principal = $local->es_principal
                ? '<i class="bi bi-check-circle-fill text-green-600"
                     title="Local activo"></i>'
                : '';

            $nombre = $local->nombre;

            return '
                <p class="block text-sm text-slate-800 flex items-center flex-nowrap gap-1">
                    ' . $es_principal . '
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
                ->event('local-editar')
                ->loading('Cargando local...'),

            (new TableAction('Eliminar'))
                ->color('red')
                ->event('local-eliminar')
                ->loading('Eliminando local...'),

        ])
        ->align('right');

        return [
            $nombre,

            new TextColumn(
                label: 'Descripción',
                field: 'descripcion',
            ),

            $acciones,
        ];
    }
}
