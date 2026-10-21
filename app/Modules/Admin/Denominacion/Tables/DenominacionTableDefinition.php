<?php

namespace App\Modules\Admin\Denominacion\Tables;

use App\Core\Tables\Actions\TableAction;
use App\Core\Tables\BaseTable;
use App\Core\Tables\Columns\ActionsColumn;
use App\Core\Tables\Columns\HtmlColumn;
use App\Core\Tables\Columns\TextColumn;
use App\Models\Denominacion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;

class DenominacionTableDefinition extends BaseTable
{

    #[On('denominacion-guardada')]
    public function refreshTable(): void
    {
        $this->resetPage();
    }

    public function query(): Builder
    {
        /*Log::info('DenominacionTableDefinition query: Builder', [
                         'getParameter - monedaId' => $this->getParameter('monedaId'),
                     ]);*/
        $query = Denominacion::query()

            ->when(
                filled($this->getParameter('monedaId')),
                fn ($q) => $q->where(
                    'moneda_id',
                    $this->getParameter('monedaId')
                )
            );

        return $query;
    }

    protected function searchable(): array
    {
        return [
            'nombre',
            'codigo',
        ];
    }

    public function columns(): array
    {
        $valor = (new HtmlColumn(
            label: 'Valor',
            field: 'valor',
        ))->formatStateUsing(function ($state, $denominacion) {

            $valor = $denominacion->valor ?? '';

            $color_text = $denominacion->color_text ?? '';
            $color_bg = $denominacion->color_bg ?? '';


            return '
                <p class="block text-sm text-slate-800 flex items-center flex-wrap-0">

                <span
                        class="text-xs font-semibold px-2 py-0.5 rounded-full ml-2 flex-shrink-0
                        ' . e($color_text) . '
                        ' . e($color_bg)  . '">
                    ' . e($valor) .
                '</span>

            </p>
            ';
        });

        $moneda = (new HtmlColumn(
            label: 'Moneda',
            field: 'moneda',
        ))->formatStateUsing(function ($state, $denominacion) {
            
            $moneda = $denominacion->moneda;
            $codigo = $moneda->codigo ?? '';
            $simbolo = $moneda->simbolo ?? '';
            
            
            $color_text = $moneda->color_text ?? '';
            $color_bg = $moneda->color_bg ?? '';

            return '
                <span>' . e($codigo) . '</span>

                <span
                    class="text-xs font-semibold px-2 py-0.5 rounded-full ml-2 flex-shrink-0
                    ' . $color_text . '
                    ' . $color_bg . '">
                    ' . e($simbolo) . '             
                </span>
            ';
        });

        $activa = (new HtmlColumn(
            label: 'Activa',
            field: 'activa',
            extra: true,
        ))->formatStateUsing(function ($state, $denominacion) {
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
                    wire:click="toggleActiva(' . e($denominacion->id) . ')"
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
                ->event('denominacion-editar')
                ->loading('Cargando denominacion...'),

            (new TableAction('Eliminar'))
                ->color('red')
                ->event('denominacion-eliminar')
                ->loading('Cargando denominacion...'),

        ])
        ->align('right');

        return [
            $valor,

            $moneda,

            $activa,

            $acciones,
        ];
    }
}
