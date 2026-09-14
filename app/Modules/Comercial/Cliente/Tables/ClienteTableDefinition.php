<?php

namespace App\Modules\Comercial\Cliente\Tables;

use App\Core\Tables\Actions\TableAction;
use App\Core\Tables\BaseTable;
use App\Core\Tables\Columns\ActionsColumn;
use App\Core\Tables\Columns\HtmlColumn;
use App\Core\Tables\Columns\TextColumn;
use App\Models\Cliente;
use Illuminate\Database\Eloquent\Builder;

class ClienteTableDefinition extends BaseTable
{
    public function query(): Builder
    {
        $query = Cliente::query();

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
            label: 'Nombre / Móvil',
            field: 'nombre',
        ))->formatStateUsing(function ($state, $cliente) {

            $nombre = $cliente->nombre ?? '';
            $movil = $cliente->telefono ?? 'Sin móvil';

            return '
                <p class="block text-sm text-slate-800 flex items-center flex-wrap-0">

                <span class="truncate">' . e($nombre) . '</span>

            </p>
                <p class="block text-xs text-slate-400">

                <span>' . e($movil) . '</span>

            </p>
            ';
        });

        $acciones = (new ActionsColumn(
            label: 'Acciones',
            field: 'acciones',
        ))->actions([

            (new TableAction('Editar'))
                ->color('yellow')
                ->event('cliente-editar')
                ->loading('Cargando cliente'),

            (new TableAction('Eliminar'))
                ->color('red')
                ->event('cliente-eliminar')
                ->loading('Eliminando cliente'),

        ])
        ->align('right');

        return [
            $nombre,

            new TextColumn(
                label: 'Email',
                field: 'email',
            ),

            new TextColumn(
                label: 'Carnet de identidad',
                field: 'ci',
            ),

            $acciones,
        ];
    }
}
