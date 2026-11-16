<?php

namespace App\Modules\Finanzas\BancoSucursal\Tables;

use App\Core\Tables\Actions\TableAction;
use App\Core\Tables\BaseTable;
use App\Core\Tables\Columns\ActionsColumn;
use App\Core\Tables\Columns\HtmlColumn;
use App\Core\Tables\Columns\TextColumn;
use App\Models\BancoSucursal;
use Illuminate\Database\Eloquent\Builder;

class BancoSucursalTableDefinition extends BaseTable
{
    public function query(): Builder
    {
        $query = BancoSucursal::query()
            ->with(['municipio', 'banco'])

            ->when(
                $this->getParameter('bancoId'),
                fn ($q) => $q->where(
                    'banco_id',
                        $this->getParameter('bancoId')
                )
            )

            ->when(
                $this->getParameter('provinciaId') && !$this->getParameter('municipioId'),
                fn ($q) => $q->whereHas(
                    'municipio', function ($q2){
                                    $q2->where(
                                        'provincia_id',
                                            $this->getParameter('provinciaId')
                                    );
                                 }
                )
            )

            ->when(
                $this->getParameter('municipioId'),
                fn ($q) => $q->where(
                    'municipio_id',
                        $this->getParameter('municipioId')
                )
            )

            ->orderBy('nombre');

        return $query;
    }

    protected function searchable(): array
    {
        return [
            'nombre',
            'slug',
        ];
    }

    public function columns(): array
    {
        $banco = new TextColumn(
            label: 'Banco',
            field: 'banco.nombre',
        );

        $nombre = (new HtmlColumn(
            label: 'Nombre',
            field: 'nombre',
        ))->formatStateUsing(function ($state, $sucursal) {
            return '
                <div class="space-y-1">
                    <p class="font-semibold break-words">
                        ' . e($sucursal->nombre ?? '—') . '
                    </p>
                    <p class="text-xs text-slate-500 break-words uppercase">
                        ' . e($sucursal->codigo_swift ?? '—') . '
                    </p>
                </div>
            ';
        });

        $activa = (new HtmlColumn(
            label: 'Activa',
            field: 'activa',
            extra: true,
        ))->formatStateUsing(function ($state, $sucursal) {
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
                    wire:click="toggleActiva(' . e($sucursal->id) . ')"
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
                ->event('banco-sucursal-fondo-editar')
                ->loading('Cargando sucursal...'),

            (new TableAction('Eliminar'))
                ->color('red')
                ->event('banco-sucursal-fondo-eliminar')
                ->loading('Cargando sucursal...'),

        ])
        ->align('right');

        return [
            $banco,

            $nombre,

            new TextColumn(
                label: 'Dirección',
                field: 'direccion',
            ),

            new TextColumn(
                label: 'Teléfonos',
                field: 'telefonos',
            ),

            $activa,

            $acciones,
        ];
    }
}