<?php

namespace App\Modules\Finanzas\CuentaBancaria\Tables;

use App\Core\Tables\Actions\TableAction;
use App\Core\Tables\BaseTable;
use App\Core\Tables\Columns\ActionsColumn;
use App\Core\Tables\Columns\HtmlColumn;
use App\Core\Tables\Columns\TextColumn;
use App\Models\CuentaBancaria;
use Illuminate\Database\Eloquent\Builder;

class CuentaBancariaTableDefinition extends BaseTable
{
    public function query(): Builder
    {
        return CuentaBancaria::query()
            ->with([
                'sucursal.banco',
                'sucursal.municipio',
                'moneda',
            ])

            ->when(
                $this->getParameter('monedaId'),
                fn ($q) => $q->whereHas(
                    'moneda', function ($q2){
                                    $q2->where(
                                        'moneda_id',
                                            $this->getParameter('monedaId')
                                    );
                                 }
                )
            )

            ->when(
                $this->getParameter('provinciaId') && !$this->getParameter('municipioId') && !$this->getParameter('sucursalId'),
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
                $this->getParameter('municipioId') && !$this->getParameter('sucursalId'),
                fn ($q) => $q->whereHas(
                    'sucursal', function ($q2){
                                    $q2->where(
                                        'municipio_id',
                                            $this->getParameter('municipioId')
                                    );
                                 }
                )
            )

            ->when(
                $this->getParameter('sucursalId'),
                fn ($q) => $q->where(
                    'sucursal_id',
                        $this->getParameter('sucursalId')
                )
            )

            ->orderBy('numero_cuenta');
    }

    protected function searchable(): array
    {
        return [
            'numero_cuenta',
            'titular',
            'descripcion',
        ];
    }

    public function columns(): array
    {
        $numeroCuenta = (new HtmlColumn(
            label: 'Número de cuenta',
            field: 'numero_cuenta',
        ))->formatStateUsing(function ($state, $cuenta) {
            return '
                <div class="space-y-1">
                    <p class="font-semibold break-words">
                        ' . e($cuenta->numero_cuenta ?? '—') . '
                    </p>
                    <p class="text-xs text-slate-500 break-words">
                        ' . e($cuenta->titular ?? '—') . '
                    </p>
                </div>
            ';
        });

        $banco = new TextColumn(
            label: 'Banco',
            field: 'sucursal.banco.nombre',
        );

        $sucursal = (new HtmlColumn(
            label: 'Sucursal',
            field: 'sucursal.nombre',
        ))->formatStateUsing(function ($state, $cuenta) {
            return '
                <div class="space-y-1">
                    <p class="font-medium break-words">
                        ' . e($cuenta->sucursal->nombre ?? '—') . '
                    </p>
                    <p class="text-xs text-slate-500 break-words">
                        ' . e($cuenta->sucursal->municipio->nombre ?? '—') . '
                    </p>
                </div>
            ';
        });

        $moneda = new TextColumn(
            label: 'Moneda',
            field: 'moneda.nombre',
        );

        $activa = (new HtmlColumn(
            label: 'Activa',
            field: 'activa',
            extra: true,
        ))->formatStateUsing(function ($state, $cuenta) {
            $texto = $state ? 'Sí' : 'NO';

            $colorTexto = $state
                ? 'text-green-700'
                : 'text-red-700';

            $colorFondo = $state
                ? 'bg-green-100'
                : 'bg-red-100';

            return '
                <button
                    type="button"
                    wire:click="toggleActiva(' . e($cuenta->id) . ')"
                    title="Clic para cambiar"
                    class="text-xs font-semibold px-2 py-0.5 rounded-full ml-2 flex-shrink-0
                    ' . $colorTexto . '
                    ' . $colorFondo . '">
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
                ->event('cuenta-bancaria-editar')
                ->loading('Cargando cuenta...'),

            (new TableAction('Eliminar'))
                ->color('red')
                ->event('cuenta-bancaria-eliminar')
                ->loading('Cargando cuenta...'),

        ])->align('right');

        return [
            $banco,
            $sucursal,
            $numeroCuenta,
            $moneda,
            $activa,
            $acciones,
        ];
    }
}
