<?php

namespace App\Modules\Finanzas\Banco\Tables;

use App\Core\Tables\Actions\TableAction;
use App\Core\Tables\BaseTable;
use App\Core\Tables\Columns\ActionsColumn;
use App\Core\Tables\Columns\HtmlColumn;
use App\Core\Tables\Columns\TextColumn;
use App\Models\Banco;
use App\Modules\Configuracion\SetupConfig\Services\SetupConfigService;
use Illuminate\Database\Eloquent\Builder;

class BancoTableDefinition extends BaseTable
{
    public function query(): Builder
    {
        $query = Banco::query()
            ->with('municipio')

            ->when(
                filled($this->getParameter('provinciaId') && !$this->getParameter('municipioId'))
                && $this->getParameter('provinciaId') && !$this->getParameter('municipioId'),
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
                filled($this->getParameter('municipioId')) && $this->getParameter('municipioId'),
                fn ($q) => $q->where(
                    'municipio_id',
                        $this->getParameter('municipioId')
                )
            )

            ->orderBy('nombre');

        // $sql = vsprintf(
        //     str_replace('?', "'%s'", $query->toSql()),
        //     $query->getBindings()
        // );
        // \Log::info('BancoTableDefinition->query', [
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
            'nombre',
            'slug',
        ];
    }

    public function columns(): array
    {
        $nombre = (new HtmlColumn(
            label: 'Nombre',
            field: 'nombre',
        ))->formatStateUsing(function ($state, $banco) {
            $alias = $banco->alias ? ' (' . $banco->alias . ')' : '';
            return '
                <div class="space-y-1">
                    <p class="font-semibold break-words">
                        ' . e($banco->nombre ?? '—') . $alias . '
                    </p>
                    <p class="text-xs text-slate-500 break-words uppercase">
                        ' . e($banco->codigo_swift ?? '—') . '
                    </p>
                </div>
            ';
        });

        $activo = (new HtmlColumn(
            label: 'Activo',
            field: 'activo',
            extra: true,
        ))->formatStateUsing(function ($state, $banco) {
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
                    wire:click="toggleActivo(' . e($banco->id) . ')"
                    title="Clic para cambiar"
                    class="text-xs font-semibold px-2 py-0.5 rounded-full ml-2 flex-shrink-0
                    ' . $color_text . '
                    ' . $color_bg . '">
                    ' . e($texto) . '
                </button>
            ';
        });

        $porDefecto = (new HtmlColumn(
            label: 'Por Defecto',
            field: 'codigo_swift',
            extra: true,
        ))->formatStateUsing(function ($state, $banco) {
            $banco_default = app(SetupConfigService::class)->get('banco_default', '');
            $texto = ($state === $banco_default) ? 'Sí' : 'NO';

            $color_text = ($state === $banco_default)
                ? 'text-green-700'
                : 'text-emerald-700';

            $color_bg = ($state === $banco_default)
                ? 'bg-emerald-100'
                : 'bg-orange-100';

            return '
                <button
                    type="button"
                    wire:click="togglePorDefecto(' . e($banco->id) . ')"
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
                ->event('banco-editar')
                ->loading('Cargando banco...'),

            (new TableAction('Eliminar'))
                ->color('red')
                ->event('banco-eliminar')
                ->loading('Cargando banco...'),

        ])
        ->align('right');

        return [
            $nombre,

            new TextColumn(
                label: 'Dirección',
                field: 'direccion',
            ),

            new TextColumn(
                label: 'Teléfonos',
                field: 'telefonos',
            ),

            $activo,

            $porDefecto,

            $acciones,
        ];
    }
}