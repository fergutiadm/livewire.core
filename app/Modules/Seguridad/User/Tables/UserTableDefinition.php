<?php

namespace App\Modules\Seguridad\User\Tables;

use App\Core\Tables\Actions\TableAction;
use App\Core\Tables\BaseTable;
use App\Core\Tables\Columns\ActionsColumn;
use App\Core\Tables\Columns\HtmlColumn;
use App\Core\Tables\Columns\TextColumn;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class UserTableDefinition extends BaseTable
{
    public function query(): Builder
    {
        $query = User::query();

        return $query;
    }

    protected function searchable(): array
    {
        return [
            'nombre',
            'email',
            'movil',
        ];
    }

    public function columns(): array
    {
        $nombre = (new HtmlColumn(
            label: 'Nombre / Móvil',
            field: 'nombre',
        ))->formatStateUsing(function ($state, $cliente) {

            $nombre = $cliente->name ?? '';
            $movil = $cliente->movil ?? 'Sin móvil';

            return '
                <p class="block text-sm text-slate-800 flex items-center flex-wrap-0">

                <span class="truncate">' . e($nombre) . '</span>

            </p>
                <p class="block text-xs text-slate-400">

                <span>' . e($movil) . '</span>

            </p>
            ';
        });

        $rol = (new HtmlColumn(
            label: 'Rol',
            field: 'roles',
        ))->formatStateUsing(function ($state, $user) {

            $roles = $user->roles
                ->pluck('name')
                ->implode(', ');

            return '<span class="px-2 py-0.5 text-[10px] uppercase font-bold rounded-full bg-indigo-100 text-indigo-700">'
            . e($roles ?: 'Sin rol') .
            '</span>';
        });

        $acciones = (new ActionsColumn(
            label: 'Acciones',
            field: 'acciones',
        ))->actions([

            (new TableAction('Editar'))
                ->color('yellow')
                ->event('user-editar'),

            (new TableAction('Eliminar'))
                ->color('red')
                ->event('user-eliminar'),

        ])
        ->align('right');

        return [
            $nombre,
            new TextColumn(
                label: 'Correo',
                field: 'email',
            ),

            $rol,

            $acciones,
        ];
    }
}
