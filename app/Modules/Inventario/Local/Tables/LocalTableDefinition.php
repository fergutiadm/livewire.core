<?php

namespace App\Modules\Inventario\Local\Tables;

use App\Core\Tables\Actions\TableAction;
use App\Core\Tables\BaseTable;
use App\Core\Tables\Columns\ActionsColumn;
use App\Core\Tables\Columns\TextColumn;
use App\Models\Local;
use Illuminate\Database\Eloquent\Builder;

class LocalTableDefinition extends BaseTable
{
    public function query(): Builder
    {
        $query = Local::query();

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
            new TextColumn(
                label: 'Nombre',
                field: 'nombre',
            ),

            new TextColumn(
                label: 'Descripción',
                field: 'descripcion',
            ),

            $acciones,
        ];
    }
}
