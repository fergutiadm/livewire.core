<?php

namespace App\Modules\Configuracion\Municipio\Tables;

use App\Core\Tables\Actions\TableAction;
use App\Core\Tables\BaseTable;
use App\Core\Tables\Columns\ActionsColumn;
use App\Core\Tables\Columns\HtmlColumn;
use App\Core\Tables\Columns\TextColumn;
use App\Models\Municipio;
use App\Modules\Configuracion\SetupConfig\Services\SetupConfigService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

class MunicipioTableDefinition extends BaseTable
{
    public function query(): Builder
    {
        $query = Municipio::query()
            ->with('provincia')

            ->when(
                filled($this->getParameter('provinciaId')),
                fn ($q) => $q->where(
                    'provincia_id',
                        $this->getParameter('provinciaId')
                )
            )

            // ->orderBy('provincia.nombre')
            ->orderBy('nombre');

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
            'nombre',
            'slug',
        ];
    }

    public function columns(): array
    {
        $provincia = new TextColumn(
            label: 'Provincia',
            field: 'provincia.nombre',
        );

        $nombre = (new HtmlColumn(
            label: 'Nombre',
            field: 'nombre',
        ))->formatStateUsing(function ($state, $concepto) {
            return '
                <div class="space-y-1">
                    <p class="font-semibold break-words">
                        ' . e($concepto->nombre ?? '—') . '
                    </p>
                    <p class="text-xs text-slate-500 break-words">
                        ' . e($concepto->slug ?? '—') . '
                    </p>
                </div>
            ';
        });

        $activo = (new HtmlColumn(
            label: 'Activo',
            field: 'activo',
            extra: true,
        ))->formatStateUsing(function ($state, $concepto) {
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
                    wire:click="toggleActivo(' . e($concepto->id) . ')"
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
            field: 'slug',
            extra: true,
        ))->formatStateUsing(function ($state, $municipio) {
            $municipio_default = app(SetupConfigService::class)->get('municipio_default', '');
            $texto = ($state === $municipio_default) ? 'Sí' : 'NO';

            $color_text = ($state === $municipio_default)
                ? 'text-green-700'
                : 'text-emerald-700';

            $color_bg = ($state === $municipio_default)
                ? 'bg-emerald-100'
                : 'bg-orange-100';

            return '
                <button
                    type="button"
                    wire:click="togglePorDefecto(' . e($municipio->id) . ')"
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
                ->event('municipio-editar')
                ->loading('Cargando municipio...'),

            (new TableAction('Eliminar'))
                ->color('red')
                ->event('municipio-eliminar')
                ->loading('Cargando municipio...'),

        ])
        ->align('right');

        return [
            $provincia,

            $nombre,

            $activo,

            $porDefecto,

            $acciones,
        ];
    }
}