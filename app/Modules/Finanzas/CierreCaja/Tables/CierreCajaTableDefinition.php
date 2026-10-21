<?php

namespace App\Modules\Finanzas\CierreCaja\Tables;

use App\Core\Tables\Actions\TableAction;
use App\Core\Tables\BaseTable;
use App\Core\Tables\Columns\ActionsColumn;
use App\Core\Tables\Columns\HtmlColumn;
use App\Core\Tables\Columns\TextColumn;
use App\Models\CierreCaja;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

class CierreCajaTableDefinition extends BaseTable
{
    public function query(): Builder
    {
        $query = CierreCaja::query()

            ->when(
                filled($this->getParameter('localId')),
                fn ($q) => $q->where(
                    'local_id',
                        $this->getParameter('localId')
                )
            )

            ->when(
                filled($this->getParameter('periodoContableId')),
                fn ($q) => $q->where(
                    'periodo_contable_id',
                        $this->getParameter('periodoContableId')
                )
            )

            ->when(
                filled($this->getParameter('cajaId')),
                fn ($q) => $q->where(
                    'caja_id',
                        $this->getParameter('cajaId')
                )
            )

            ->when(
                filled($this->getParameter('operadorId')),
                fn ($q) => $q->where(
                    'operador_id',
                        $this->getParameter('operadorId')
                )
            );

            $sql = vsprintf(
                str_replace('?', "'%s'", $query->toSql()),
                $query->getBindings()
            );
            Log::info('CierreCajaTableDefinition->query', [
                'sql' => $sql,
                '$query->toSql()' => $query->toSql(),
                ' $query->getBindings()' => $query->getBindings(),
                '$query->count()' => $query->count()]
            );

        return $query;
    }

    // protected function searchable(): array
    // {
    //     return [
    //         'local',
    //     ];
    // }

    public function columns(): array
    {
        $caja = (new HtmlColumn(
            label: 'Caja',
            field: 'caja',
        ))->formatStateUsing(function ($state, $cierreCaja) {
           $caja = $cierreCaja->caja?->nombre;
            // $color_text = $state
            //     ? 'text-green-700'
            //     : 'text-red-700';

            // $color_bg = $state
            //     ? 'bg-green-100'
            //     : 'bg-red-100';

            return '
                <span
                    class="text-xs font-semibold py-0.5 rounded-full ml-2 flex-shrink-0">
                    ' . e($caja) . '
                </span>
            ';
        });

        $operador = (new HtmlColumn(
            label: 'Operador',
            field: 'operador',
        ))->formatStateUsing(function ($state, $cierreCaja) {
           $operador = $cierreCaja->operador?->name;

            return '
                <span
                    class="text-xs font-semibold py-0.5 rounded-full ml-2 flex-shrink-0">
                    ' . e($operador) . '
                </span>
            ';
        });

        $fecha = (new HtmlColumn(
            label: 'Fecha',
            field: 'fecha',
        ))->formatStateUsing(function ($state, $cierreCaja) {

            $fecha = $cierreCaja->fecha ? format_fecha($cierreCaja->fecha) : '';


            return '
                <p class="block text-sm text-slate-800 flex items-center flex-wrap-0">

                <span>' . e($fecha) . '</span>
            </p>
            ';
        });

        $monedas = (new HtmlColumn(
            label: 'Monto',
            field: 'fecha',
        ))->formatStateUsing(function ($state, $cierreCaja) {

            $cierreCajaMonedas = $cierreCaja->cierres_caja_moneda ?? null;
            $filas_moneda = '<div class="
                                            w-full items-center justify-center
                                            rounded-lg border px-2 text-center
                                            transition
                                            border-slate-400 bg-slate-50
                                        ">';
            
            foreach($cierreCajaMonedas as $item){
                $boton_detalles = '<button 
                            type="button"
                            wire:click="$dispatch(\'cierre-caja-moneda-detalles-editar\', { id: ' . $item->id . ' })" 
                            x-on:click="$dispatch(\'loading-start\', { message: \'Cargando denominaciones...\' })"
                            class="
                                inline-flex 
                                items-center gap-1 
                                rounded-lg 
                                px-3 
                                py-1.5 
                                text-xs 
                                font-medium 
                                text-white 
                                transition 
                                bg-slate-500 
                                hover:bg-slate-800
                            "
                        >
                            <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
                            Detalles
                        </button>';
                $moneda = $item->moneda?->codigo ?? '';
                $color_bg = $item->moneda?->color_bg ?? '';
                $color_text = $item->moneda?->color_text ?? '';
                $filas_moneda .= '<div class="grid grid-cols-1 md:grid-cols-4 gap-2 py-2">';
                $filas_moneda .= '<div>
                                    <span
                                        class="text-xs font-semibold px-2 py-0.5 rounded-full ml-2 flex-shrink-0
                                        ' . e($color_text) . '
                                        ' . e($color_bg)  . '">
                                    ' . e($moneda) .
                                    '</span>
                                   </div>';
                $filas_moneda .= '<div>' . $item->monto_esperado . '</div>';
                $filas_moneda .= '<div>' . $item->estado . '</div>';
                $filas_moneda .= '<div>' . $boton_detalles . '</div>';
                $filas_moneda .= "</div>";
            }

            $filas_moneda .= "</div>";


            return $filas_moneda;
        });

        $acciones = (new ActionsColumn(
            label: 'Acciones',
            field: 'acciones',
        ))->actions([

            (new TableAction('Editar'))
                ->color('yellow')
                ->event('cierre-caja-editar')
                ->loading('Cargando caja...'),

            (new TableAction('Eliminar'))
                ->color('red')
                ->event('cierre-caja-eliminar')
                ->loading('Cargando caja...'),

            /*(new TableAction('Detalles'))
                ->color('magenta')
                ->event('cierre-caja-moneda-detalles-editar'),*/

        ])
        ->align('right');

        return [
            $caja,
            
            $operador,

            $fecha,

            /*new TextColumn(
                label: 'Estado',
                field: 'estado',
            ),*/

            $monedas,

            $acciones,
        ];
    }
}
