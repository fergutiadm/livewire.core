<?php

namespace App\Modules\Gastos\GastoPago\Tables;

use App\Core\Tables\Actions\TableAction;
use App\Core\Tables\BaseTable;
use App\Core\Tables\Columns\ActionsColumn;
use App\Core\Tables\Columns\HtmlColumn;
use App\Core\Tables\Columns\TextColumn;
use App\Models\GastoPago;
use Illuminate\Database\Eloquent\Builder;

class GastoPagoTableDefinition extends BaseTable
{
    public ?int $gastoId = null;
    public ?int $tarjetaMagneticaId = null;
    public ?int $monedaId = null;
    public ?int $operadorId = null;

    public function __construct(array $params = [])
    {
        $this->localId = $params['localId'] ?? null;
        $this->tarjetaMagneticaId = $params['tarjetaMagneticaId'] ?? null;
        $this->monedaId = $params['monedaId'] ?? null;
        $this->operadorId = $params['operadorId'] ?? null;
    }

    protected function searchable(): array
    {
        return [
            'descripcion',
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
                filled($this->getParameter('tarjetaMagneticaId')),
                fn ($q) => $q->where(
                    'tarjeta_magnetica_id',
                    $this->getParameter('tarjetaMagneticaId')
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
                filled($this->getParameter('operadorId')),
                fn ($q) => $q->where(
                    'operador_id',
                    $this->getParameter('operadorId')
                )
            );

            // $sql = vsprintf(
            //     str_replace('?', "'%s'", $query->toSql()),
            //     $query->getBindings()
            // );
            // \Log::info('GastoPagoTableDefinition->query', [
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
                ->event('gasto-pago-editar')
                ->loading('Cargando gasto pago...'),

            (new TableAction('Eliminar'))
                ->color('red')
                ->event('gasto-pago-eliminar')
                ->loading('Eliminando gasto pago...'),

        ])
        ->align('right');

        return [
            new TextColumn(
                label: 'Operador',
                field: 'operador_id',
            ),

            new TextColumn(
                label: 'Metodo',
                field: 'metodo',
            ),

            new TextColumn(
                label: 'Numero-Tarjeta',
                field: 'numero_tarjeta',
            ),

            new TextColumn(
                label: 'Moneda',
                field: 'moneda_id',
            ),

            new TextColumn(
                label: 'Cantidad',
                field: 'cantidad',
            ),

            new TextColumn(
                label: 'Tasa-Cambio',
                field: 'tasa_cambio',
            ),

            new TextColumn(
                label: 'Fecha-Pago',
                field: 'fecha_pago',
            ),

            new TextColumn(
                label: 'Descripcion',
                field: 'descripcion',
            ),

            $acciones,
        ];
    }
}