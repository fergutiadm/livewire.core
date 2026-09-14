<?php

namespace App\Modules\Comercial\Gestor\Tables;

use App\Core\Tables\Actions\TableAction;
use App\Core\Tables\BaseTable;
use App\Core\Tables\Columns\ActionsColumn;
use App\Core\Tables\Columns\HtmlColumn;
use App\Core\Tables\Columns\TextColumn;
use App\Models\Gestor;
use Illuminate\Database\Eloquent\Builder;

class GestorTableDefinition extends BaseTable
{
    public function query(): Builder
    {
        $query = Gestor::query()
            ->with('user');

        return $query;
    }

    protected function searchable(): array
    {
        return [
            'nombre_comercial',
            'slug',
        ];
    }

    public function columns(): array
    {
        $usuario = (new HtmlColumn(
            label: 'Usuario',
            field: 'user',
        ))->formatStateUsing(function ($state, $gestor) {
            $user = $gestor->user;

            if (!$user) {
                return '<span class="text-sm text-red-600">Sin usuario</span>';
            }

            $nombre = trim(
                ($user->name ?? 'Sin nombre')
            );

            if ($nombre === '') {
                $nombre = $user->email ?? 'Sin nombre';
            }

            return '
                <div class="flex flex-col">
                    <span class="text-sm text-slate-800">
                        ' . e($nombre) . '
                    </span>
                    <span class="text-xs text-slate-500">
                        ' . e($user->email ?? '') . '
                    </span>
                </div>
            ';
        });

        $activo = (new HtmlColumn(
            label: 'Activo',
            field: 'activo',
        ))->formatStateUsing(function ($state, $gestor) {
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
                    wire:click="toggleActivo(' . e($gestor->id) . ')"
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
                ->event('gestor-editar')
                ->loading('Cargando gestor...'),

            (new TableAction('Eliminar'))
                ->color('red')
                ->event('gestor-eliminar')
                ->loading('Eliminando gestor...'),
        ])
        ->align('right');

        return [
            $usuario,

            new TextColumn(
                label: 'Nombre comercial',
                field: 'nombre_comercial',
            ),

            new TextColumn(
                label: 'Slug',
                field: 'slug',
            ),

            $activo,

            $acciones,
        ];
    }
}