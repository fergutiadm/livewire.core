<?php

namespace App\Modules\Gastos\TipoGasto\Tables;

use App\Core\Tables\Actions\TableAction;
use App\Core\Tables\BaseTable;
use App\Core\Tables\Columns\ActionsColumn;
use App\Core\Tables\Columns\HtmlColumn;
use App\Core\Tables\Columns\TextColumn;
use App\Models\TipoGasto;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

class TipoGastoTableDefinition extends BaseTable
{
    public ?int $localId = null;

    public function __construct(array $params = [])
    {
        $this->localId = $params['localId'] ?? null;
    }

    protected function searchable(): array
    {
        return [
            'nombre',
            'descripcion',
        ];
    }

    

    public function query(): Builder
    {
        $query = TipoGasto::query()

            ->when(
                filled($this->getParameter('localId')),
                fn ($q) => $q->where(
                    'local_id',
                    $this->getParameter('localId')
                )
            );

            // $sql = vsprintf(
            //     str_replace('?', "'%s'", $query->toSql()),
            //     $query->getBindings()
            // );
            // \Log::info('TipoGastoTableDefinition->query', [
            //     'sql' => $sql,
            //     '$query->toSql()' => $query->toSql(),
            //     ' $query->getBindings()' => $query->getBindings(),
            //     '$query->count()' => $query->count()]
            // );
            // dd(
            //     $sql,
            //     $query->toSql(),
            //     $query->getBindings(),
            //     $query->count()
            // );

        return $query;
    }

    public function columns(): array
    {
        $acciones = (new ActionsColumn(
            label: 'Acciones',
            field: 'acciones',
        ))->actions([

            (new TableAction('Editar'))
                ->color('yellow')
                ->event('tipo-gasto-editar')
                ->loading('Cargando tipo de gasto...'),

            /* */
            (new TableAction('Eliminar'))
                ->color('red')
                ->event('tipo-gasto-eliminar')
                ->loading('Eliminando tipo de gasto...'),
            /* */

        ])
        ->align('right');

        $nombre = (new HtmlColumn(
            label: 'Nombre',
            field: 'nombre',
        ))->formatStateUsing(function ($state, $tipo_gasto) {

            $color_text = $tipo_gasto->color_text ?? '';
            $color_bg = $tipo_gasto->color_bg ?? '';


            return '
                <p class="block text-sm text-slate-800 flex items-center flex-wrap-0">

                <span
                        class="text-xs font-semibold px-2 py-0.5 rounded-full ml-2 flex-shrink-0
                        ' . e($color_text) . '
                        ' . e($color_bg)  . '">
                        ' . e($state) .
                '</span>

            </p>
            ';
        });

        $activo = (new HtmlColumn(
            label: 'Activo',
            field: 'activo',
            extra: true,
        ))->formatStateUsing(function ($state, $tipoGasto) {
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
                    wire:click="toggleActivo(' . e($tipoGasto->id) . ')"
                    title="Clic para cambiar"
                    class="text-xs font-semibold px-2 py-0.5 rounded-full ml-2 flex-shrink-0
                    ' . $color_text . '
                    ' . $color_bg . '">
                    ' . e($texto) . '
                </button>
            ';
        });

        $fijo = (new HtmlColumn(
            label: 'Fijo',
            field: 'fijo',
            extra: true,
        ))->formatStateUsing(function ($state, $tipoGasto) {
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
                    wire:click="toggleFijo(' . e($tipoGasto->id) . ')"
                    title="Clic para cambiar"
                    class="text-xs font-semibold px-2 py-0.5 rounded-full ml-2 flex-shrink-0
                    ' . $color_text . '
                    ' . $color_bg . '">
                    ' . e($texto) . '
                </button>
            ';
        });

        return [
           $nombre,

            new TextColumn(
                label: 'Descripcion',
                field: 'descripcion',
            ),

            $fijo,

            $activo,

            $acciones,
        ];
    }
}