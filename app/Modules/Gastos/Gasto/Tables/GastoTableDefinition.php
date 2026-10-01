<?php

namespace App\Modules\Gastos\Gasto\Tables;

use App\Core\Tables\Actions\TableAction;
use App\Core\Tables\BaseTable;
use App\Core\Tables\Columns\ActionsColumn;
use App\Core\Tables\Columns\HtmlColumn;
use App\Core\Tables\Columns\TextColumn;
use App\Models\Gasto;
use Illuminate\Database\Eloquent\Builder;

class GastoTableDefinition extends BaseTable
{
    public ?int $localId = null;
    public ?int $tipoGastoId = null;
    public ?int $periodoContableId = null;
    public ?int $operadorId = null;
    public ?int $monedaId = null;

    public function __construct(array $params = [])
    {
        $this->localId = $params['localId'] ?? null;
        $this->tipoGastoId = $params['tipoGastoId'] ?? null;
        $this->periodoContableId = $params['periodoContableId'] ?? null;
        $this->operadorId = $params['operadorId'] ?? null;
        $this->monedaId = $params['monedaId'] ?? null;
    }

    protected function searchable(): array
    {
        return [
            'nombre',
            'codigo',
        ];
    }

    public function query(): Builder
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
                filled($this->getParameter('operadorId')),
                fn ($q) => $q->where(
                    'operador_id',
                    $this->getParameter('operadorId')
                )
            )

            ->when(
                filled($this->getParameter('monedaId')),
                fn ($q) => $q->where(
                    'moneda_id',
                    $this->getParameter('monedaId')
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

    public function columns(): array
    {
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
            new TextColumn(
                label: 'Cantidad',
                field: 'cantidad',
            ),

            new TextColumn(
                label: 'Valor',
                field: 'valor',
            ),

            new TextColumn(
                label: 'Moneda',
                field: 'moneda_id',
            ),

            new TextColumn(
                label: 'Tasa-Cambio',
                field: 'tasa_cambio',
            ),

            new TextColumn(
                label: 'Tasa-Cambio-USD',
                field: 'tasa_cambio_usd',
            ),

            new TextColumn(
                label: 'Chequeado',
                field: 'chequeado',
            ),

            new TextColumn(
                label: 'Fecha-Chequeado',
                field: 'fecha_chequeado',
            ),

            $acciones,
        ];
    }
}