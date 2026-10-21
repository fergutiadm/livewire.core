<?php

namespace App\Modules\Gastos\Gasto\Tables;

use App\Core\Tables\Actions\TableAction;
use App\Core\Tables\BaseTable;
use App\Core\Tables\Columns\ActionsColumn;
use App\Core\Tables\Columns\HtmlColumn;
use App\Core\Tables\Columns\TextColumn;
use App\Models\Gasto;
use App\Services\MonedaService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

class GastoTableDefinition extends BaseTable
{
    protected function searchable(): array
    {
        return [
            'nombre',
            'codigo',
        ];
    }

    public function query(): Builder
    {
        // Log::info('GastoTableDefinition - Builder',
        //     [
        //         'localId' => $this->getParameter('localId'),
        //         'tipoGastoId' => $this->getParameter('tipoGastoId'),
        //         'especialistaId' => $this->getParameter('especialistaId'),
        //         'responsableId' => $this->getParameter('responsableId'),
        //         'operadorId' => $this->getParameter('operadorId'),
        //         'monedaId' => $this->getParameter('monedaId'),
        //         'periodoContableId' => $this->getParameter('periodoContableId'),
        //         'fecha' => $this->getParameter('fecha'),
        //     ]
        // );
        
        $query = Gasto::query()

            ->when(
                filled($this->getParameter('localId')),
                fn ($q) => $q->where(
                    'local_id',
                        $this->getParameter('localId')
                )
            )

            ->when(
                filled($this->getParameter('tipoGastoId')),
                fn ($q) => $q->where(
                    'tipo_gasto_id',
                    $this->getParameter('tipoGastoId')
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
                filled($this->getParameter('fecha')),
                fn ($q) => $q->where(
                    'fecha',
                    $this->getParameter('fecha')
                )
            )

            /* * /
            ->when(
                filled($this->getParameter('operadorId')),
                fn ($q) => $q->where(
                    'operador_id',
                    $this->getParameter('operadorId')
                )
            )
            /* */

            /* * /
            ->when(
                filled($this->getParameter('monedaId')),
                fn ($q) => $q->where(
                    'moneda_id',
                    $this->getParameter('monedaId')
                )
            )
            /* */
            ;

            // $sql = vsprintf(
            //     str_replace('?', "'%s'", $query->toSql()),
            //     $query->getBindings()
            // );
            // \Log::info('GastoTableDefinition->query', [
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
    
    public function queryLocalPeriodo(): Builder
    {
        $query = Gasto::query()

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
            );

            // $sql = vsprintf(
            //     str_replace('?', "'%s'", $query->toSql()),
            //     $query->getBindings()
            // );
            // \Log::info('GastoTableDefinition->query', [
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

        /**
         * Devuelve las sumatorias exactas acumuladas de la consulta filtrada.
         *
         * Los Gastos pueden estar registrados en diferentes monedas,
         * por lo que no se suman directamente sus importes originales.
         *
         * Se calculan:
         * - cantidad total
         * - importe total convertido a moneda principal (CUP)
         * - importe total convertido a USD
         */
        public function getTotals(&$data_decorate): array
        {
            $baseQuery = $this->query();
            $baseQuery->getQuery()->columns = [];

            $totales = $baseQuery
                ->selectRaw('SUM(cantidad) as total_cantidad')
                ->selectRaw('
                    SUM(
                        cantidad * valor * tasa_cambio
                    ) as total_base
                ')
                ->selectRaw('
                    SUM(
                        (cantidad * valor * tasa_cambio)
                        / NULLIF(tasa_cambio_usd, 0)
                    ) as total_usd
                ')
                ->first();

            /*
            |--------------------------------------------------------------------------
            | Aproximación de presentación
            |--------------------------------------------------------------------------
            |
            | Los importes convertidos a USD se muestran siempre hacia abajo
            | al múltiplo de 0.5 inmediatamente inferior.
            |
            | Ejemplo:
            | 55.777... -> 55.50
            |
            */
            $totalUsd = (float) ($totales->total_usd ?? 0);

            $totalUsd = floor($totalUsd * 2) / 2;

            $monedaService = app(MonedaService::class);

            $decorate_cup = $monedaService->decorateCodigo(
                MonedaService::CODIGO_CUP
            );

            $decorate_usd = $monedaService->decorateCodigo(
                MonedaService::CODIGO_USD
            );


            $data_decorate = [
                                'decorate_base' => $decorate_cup,

                                'decorate_usd' => $decorate_usd
                            ];

            return [
                        'cantidad' => number_format(
                            (float) ($totales->total_cantidad ?? 0),
                            2,
                            '.',
                            ''
                        ),

                        'base' => number_format(
                            (float) ($totales->total_base ?? 0),
                            2,
                            '.',
                            ''
                        ),

                        'usd' => number_format(
                            $totalUsd,
                            2,
                            '.',
                            ''
                        ),
                    ];
        }

        /**
         * Devuelve las sumatorias exactas acumuladas de la consulta filtrada.
         *
         * Los Gastos pueden estar registrados en diferentes monedas,
         * por lo que no se suman directamente sus importes originales.
         *
         * Se calculan:
         * - cantidad total
         * - importe total convertido a moneda principal (CUP)
         * - importe total convertido a USD
         */
        public function getTotalsLocalPeriodo(&$data_decorate): array
        {
            $baseQuery = $this->queryLocalPeriodo();
            $baseQuery->getQuery()->columns = [];

            $sql = vsprintf(
                str_replace('?', "'%s'", $baseQuery->toSql()),
                $baseQuery->getBindings()
            );

            $totales = $baseQuery
                ->selectRaw('SUM(cantidad) as total_cantidad')
                ->selectRaw('
                    SUM(
                        cantidad * valor * tasa_cambio
                    ) as total_base
                ')
                ->selectRaw('
                    SUM(
                        (cantidad * valor * tasa_cambio)
                        / NULLIF(tasa_cambio_usd, 0)
                    ) as total_usd
                ')
                ->first();

            /*
            |--------------------------------------------------------------------------
            | Aproximación de presentación
            |--------------------------------------------------------------------------
            |
            | Los importes convertidos a USD se muestran siempre hacia abajo
            | al múltiplo de 0.5 inmediatamente inferior.
            |
            | Ejemplo:
            | 55.777... -> 55.50
            |
            */
            $totalUsd = (float) ($totales->total_usd ?? 0);

            $totalUsd = floor($totalUsd * 2) / 2;

            $monedaService = app(MonedaService::class);

            $decorate_cup = $monedaService->decorateCodigo(
                MonedaService::CODIGO_CUP
            );

            $decorate_usd = $monedaService->decorateCodigo(
                MonedaService::CODIGO_USD
            );


            $data_decorate = [
                                'decorate_base' => $decorate_cup,

                                'decorate_usd' => $decorate_usd
                            ];

            return [
                        'cantidad' => number_format(
                            (float) ($totales->total_cantidad ?? 0),
                            2,
                            '.',
                            ''
                        ),

                        'base' => number_format(
                            (float) ($totales->total_base ?? 0),
                            2,
                            '.',
                            ''
                        ),

                        'usd' => number_format(
                            $totalUsd,
                            2,
                            '.',
                            ''
                        ),
                    ];
        }


    public function columns(): array
    {
        $chequeado = (new HtmlColumn(
            label: 'Chequeado',
            field: 'chequeado',
            extra: true,
        ))->formatStateUsing(function ($state, $gasto) {
            
            $fecha_chequeado = $gasto->fecha_chequeado ? format_fecha($gasto->fecha_chequeado) : '';
            
            $texto = $state ? $fecha_chequeado : 'NO';
            
            $span_quitar = $state && $fecha_chequeado != '' ? '<span class="text-xs font-semibold px-2 py-0.5 text-red-700">X</span>' : '';

            $color_text = $state
                ? 'text-green-700'
                : 'text-red-700';

            $color_bg = $state
                ? 'bg-green-100'
                : 'bg-red-100';

            return '
                <button
                    type="button"
                    wire:click="toggleChequeado(' . e($gasto->id) . ')"
                    title="Clic para cambiar"
                    class="text-xs font-semibold px-2 py-0.5 rounded-full ml-2 flex-shrink-0
                    ' . $color_text . '
                    ' . $color_bg . '">
                    ' . e($texto) . '
                    ' . $span_quitar . '                    
                </button>
            ';
        });

        $moneda = (new HtmlColumn(
            label: 'Moneda',
            field: 'moneda',
        ))->formatStateUsing(function ($state, $gasto) {
            
            $moneda = $gasto->moneda;
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

        $tipoGasto = (new HtmlColumn(
            label: 'Tipo',
            field: 'tipo_gasto_id',
        ))->formatStateUsing(function ($state, $gasto) {

            $tipo_gasto = $gasto->tipoGasto;

            $nombre = $tipo_gasto->nombre ?? '';

            $color_text = $tipo_gasto->color_text ?? '';
            $color_bg = $tipo_gasto->color_bg ?? '';


            return '
                <p class="block text-sm text-slate-800 flex items-center flex-wrap-0">

                <span
                        class="text-xs font-semibold px-2 py-0.5 rounded-full ml-2 flex-shrink-0
                        ' . e($color_text) . '
                        ' . e($color_bg)  . '">
                    ' . e($nombre) .
                '</span>

            </p>
            ';
        });

        $acciones = (new ActionsColumn(
            label: 'Acciones',
            field: 'acciones',
        ))->actions([

            (new TableAction('Editar'))
                ->color('yellow')
                ->event('gasto-editar')
                ->loading('Cargando gasto...'),

            (new TableAction('Eliminar'))
                ->color('red')
                ->event('gasto-eliminar')
                ->loading('Eliminando gasto...'),

        ])
        ->align('right');

        return [
            $tipoGasto,

            new TextColumn(
                label: 'Cantidad',
                field: 'cantidad',
            ),

            new TextColumn(
                label: 'Valor',
                field: 'valor',
            ),

            // 🔥 NUEVA COLUMNA: Calcula y muestra el importe formateado de forma limpia
            (new TextColumn(
                label: 'Importe',
                field: 'importe',
            )),

            $moneda,

            new TextColumn(
                label: 'Tasa-Cambio',
                field: 'tasa_cambio',
            ),

            /*new TextColumn(
                label: 'Tasa-Cambio-USD',
                field: 'tasa_cambio_usd',
            ),*/

            $chequeado,

            /*new TextColumn(
                label: 'Fecha-Chequeado',
                field: 'fecha_chequeado',
            ),*/

            $acciones,
        ];
    }
}