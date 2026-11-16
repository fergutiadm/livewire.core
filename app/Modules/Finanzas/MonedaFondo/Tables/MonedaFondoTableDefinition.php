<?php

namespace App\Modules\Finanzas\MonedaFondo\Tables;

use App\Core\Tables\BaseTable;
use App\Core\Tables\Columns\HtmlColumn;
use App\Core\Tables\Columns\TextColumn;
use App\Models\MonedaFondo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

class MonedaFondoTableDefinition extends BaseTable
{
    public function query(): Builder
    {
        $query = MonedaFondo::query()
            ->with([
                'local',
                'periodo',
                'moneda',
                'concepto',
                'generadoPor',
            ])

            ->when(
                filled($this->getParameter('localId')),
                fn ($q) => $q->where(
                    'local_id',
                        $this->getParameter('localId')
                )
            )

            ->when(
                filled($this->getParameter('periodoId')),
                fn ($q) => $q->where(
                    'periodo_id',
                    $this->getParameter('periodoId')
                )
            )

            ->when(
                filled($this->getParameter('monedaId')),
                fn ($q) => $q->where(
                    'moneda_id',
                    $this->getParameter('monedaId')
                )
            )

            ->when(
                filled($this->getParameter('conceptoId')),
                fn ($q) => $q->where(
                    'concepto_id',
                    $this->getParameter('conceptoId')
                )
            )

            ->when(
                filled($this->getParameter('fecha')),
                fn ($q) => $q->where(
                    'fecha',
                    $this->getParameter('fecha')
                )
            )

            ->orderByDesc('fecha')
            ->orderByDesc('id');

            // $sql = vsprintf(
            //     str_replace('?', "'%s'", $query->toSql()),
            //     $query->getBindings()
            // );
            // Log::info('GastoTableDefinition->query', [
            //     'sql' => $sql,
            //     '$query->toSql()' => $query->toSql(),
            //     ' $query->getBindings()' => $query->getBindings(),
            //     '$query->count()' => $query->count()]
            // );

            return $query;
    }

    protected function searchable(): array
    {
        return [
            'descripcion',
        ];
    }

    public function columns(): array
    {
        $fecha = (new HtmlColumn(
            label: 'Fecha',
            field: 'fecha',
        ))->formatStateUsing(function ($state, $movimiento) {
            $fecha = format_fecha($movimiento->fecha);
            return '
                <p class="text-sm font-medium text-slate-800">
                    ' . e($fecha) . '
                </p>
            ';
        });

        $local = new TextColumn(
            label: 'Local',
            field: 'local.nombre',
        );

        $periodo = (new HtmlColumn(
            label: 'Periodo',
            field: 'periodo_id',
        ))->formatStateUsing(function ($state, $movimiento) {

            $periodo = $movimiento->periodo;

            if(!$periodo){
                return '<span class="text-red-700 italic">Sin periodo</span>';
            }

            $rango_fecha = format_fecha($periodo->fecha_inicio) . ' → ' . format_fecha($periodo->fecha_fin);

            return '
                <div class="space-y-0.5">
                    <p class="text-sm font-semibold text-slate-800">
                        ' . e($periodo->nombre) . '
                    </p>
                    <p class="text-[10px] text-slate-500">
                        ' . e($rango_fecha) . '
                    </p>
                </div>
            ';
        });

        // $periodo = new TextColumn(
        //     label: 'Periodo',
        //     field: 'periodo.nombre',
        // );

        $concepto = (new HtmlColumn(
            label: 'Concepto',
            field: 'concepto.entrada_salida',
        ))->formatStateUsing(function ($state, $movimiento) {

            $color_text = $state
                ? 'text-green-700'
                : 'text-red-700';

            $color_bg = $state
                ? 'bg-green-100'
                : 'bg-red-100';

            return '
                <button class="text-xs font-semibold px-2 py-1.5 rounded-full ml-2 flex-shrink-0
                    ' . $color_text . '
                    ' . $color_bg . '">
                    ' . e($movimiento->concepto->nombre ?: '—') . '
                </button>
            ';
        });

        $descripcion = (new HtmlColumn(
            label: 'Descripción',
            field: 'descripcion',
        ))->formatStateUsing(function ($state, $movimiento) {
            return '
                <p class="text-sm font-medium text-slate-800 break-words">
                    ' . e($movimiento->descripcion ?: '—') . '
                </p>
            ';
        });

        $moneda = (new HtmlColumn(
            label: 'Moneda',
            field: 'moneda_id',
        ))->formatStateUsing(function ($state, $movimiento) {

            $moneda = $movimiento->moneda;

            if(!$moneda){
                return '<span class="text-red-700 italic">Sin moneda</span>';
            }

            $codigo = $moneda->codigo ?? '';
            $simbolo = $moneda->simbolo ?? '';

            $color_text = $moneda->color_text ?? '';
            $color_bg = $moneda->color_bg ?? '';

            // $es_principal = $moneda->es_principal
            //     ? '<i class="bi bi-check-circle-fill text-green-600"
            //          title="Moneda Base"></i> '
            //     : '';


            return '
                    <span
                            class="text-xs font-semibold px-2 py-0.5 rounded-full ml-2 flex-shrink-0
                            ' . e($color_text) . '
                            ' . e($color_bg)  . '">
                        ' . e($simbolo . ' ' . $codigo) .
                    '</span>
            ';
        });
        // $moneda = (new HtmlColumn(
        //     label: 'Moneda',
        //     field: 'moneda_id',
        //     // extra: true,
        // ))->formatStateUsing(function ($state, $movimiento) {
        //     $moneda = $movimiento->moneda;

        //     if (!$moneda) {
        //         return '<span class="text-red-700 italic">Sin moneda</span>';
        //     }

        //     return '
        //         <div class="space-y-0.5">
        //             <p class="text-sm font-semibold text-slate-800">
        //                 ' . e($moneda->codigo) . '
        //             </p>
        //             <p class="text-xs text-slate-500">
        //                 ' . e($moneda->nombre) . '
        //             </p>
        //         </div>
        //     ';
        // });

        $monto = (new HtmlColumn(
            label: 'Monto',
            field: 'monto',
        ))->formatStateUsing(function ($state, $movimiento) {

            $entrada = (bool) $movimiento->concepto?->entrada_salida;

            $signo = $entrada ? '+' : '-';

            $color = $entrada
                ? 'text-green-700'
                : 'text-red-700';

            $importe = number_format(
                (float) $movimiento->monto,
                2,
                '.',
                ','
            );

            $desglose = is_array($movimiento->desglose)
                ? $movimiento->desglose
                : [];

            $detalle = '';

            if (!empty($desglose)) {

                $filas = '';

                foreach ($desglose as $card) {

                    if (!is_array($card)) {
                        continue;
                    }

                    $valor = (string) (
                        $card['valor_denominacion']
                        ?? $card['denominacion']
                        ?? '0'
                    );

                    $cantidad = (int) (
                        $card['cantidad_billetes'] ?? 0
                    );

                    if ($cantidad <= 0) {
                        continue;
                    }

                    $subtotal = bcmul(
                        $valor,
                        (string) $cantidad,
                        2
                    );

                    $filas .= '
                        <tr class="border-b border-slate-100">
                            <td class="py-1.5 pr-3 text-right">
                                ' . e(number_format((float) $valor, 2, '.', ',')) . '
                            </td>

                            <td class="py-1.5 px-3 text-center">
                                ' . $cantidad . '
                            </td>

                            <td class="py-1.5 pl-3 text-right font-medium">
                                ' . e(number_format((float) $subtotal, 2, '.', ',')) . '
                            </td>
                        </tr>
                    ';
                }


                if ($filas !== '') {

                    $detalle = '
                        <div x-data class="inline-flex ml-1">

                            <button
                                type="button"
                                x-on:click="$refs.desglose.showModal()"
                                class="inline-flex items-center justify-center
                                    w-7 h-7 rounded-full
                                    text-slate-500 hover:text-indigo-700
                                    hover:bg-indigo-50 transition"
                                title="Consultar desglose de denominaciones"
                            >
                                <i class="bi bi-receipt text-base"></i>
                            </button>

                            <dialog
                                x-ref="desglose"
                                x-on:click.self="$refs.desglose.close()"
                                class="m-auto w-[calc(100%-2rem)] max-w-sm
                                    rounded-xl border-0 p-0
                                    shadow-2xl backdrop:bg-slate-900/50"
                            >

                                <div class="bg-white rounded-xl p-4">

                                    <div class="flex items-center justify-between mb-4">

                                        <p class="text-sm font-semibold text-slate-800">
                                            Desglose de denominaciones
                                        </p>

                                        <span class="text-xs text-slate-400">
                                            Solo consulta
                                        </span>

                                    </div>

                                    <table class="w-full text-xs">

                                        <thead>
                                            <tr class="text-slate-500 border-b border-slate-200">

                                                <th class="py-2 pr-3 text-right">
                                                    Valor
                                                </th>

                                                <th class="py-2 px-3 text-center">
                                                    Cant.
                                                </th>

                                                <th class="py-2 pl-3 text-right">
                                                    Importe
                                                </th>

                                            </tr>
                                        </thead>

                                        <tbody class="text-slate-700">
                                            ' . $filas . '
                                        </tbody>

                                    </table>

                                    <div class="mt-3 pt-3 border-t border-slate-200
                                                flex justify-between text-sm font-semibold">

                                        <span>Total</span>

                                        <span class="text-slate-800">
                                            ' . e(number_format(
                                                (float) $movimiento->monto,
                                                2,
                                                '.',
                                                ','
                                            )) . '
                                        </span>

                                    </div>

                                    <div class="mt-5 flex justify-end">

                                        <form method="dialog">
                                            <button
                                                type="submit"
                                                class="btn-secondary justify-center"
                                            >
                                                Cerrar
                                            </button>
                                        </form>

                                    </div>

                                </div>

                            </dialog>

                        </div>
                    ';
                }

            }

            return '
                <div class="inline-flex items-center whitespace-nowrap">

                    <span class="font-semibold ' . $color . '">
                        ' . $signo . $importe . '
                    </span>

                    ' . $detalle . '

                </div>
            ';
        });

        $tasa = (new HtmlColumn(
            label: 'Tasa',
            field: 'tasa_cambio',
        ))->formatStateUsing(function ($state, $movimiento) {

            $tasa_cambio = number_format(
                (float) $movimiento->tasa_cambio,
                2,
                '.',
                ','
            );

            return '
                <span class="font-semibold text-slate-800">
                    ' . $tasa_cambio . '
                </span>
            ';
        });

        return [
            $fecha,
            $local,
            $periodo,
            $concepto,
            $descripcion,
            $moneda,
            $monto,
            $tasa,
        ];
    }
}