<?php

namespace App\Modules\Configuracion\SetupConfig\Tables;

use App\Core\Tables\Actions\TableAction;
use App\Core\Tables\BaseTable;
use App\Core\Tables\Columns\ActionsColumn;
use App\Core\Tables\Columns\HtmlColumn;
use App\Core\Tables\Columns\TextColumn;
use App\Models\SetupConfig;
use Illuminate\Database\Eloquent\Builder;

class SetupConfigTableDefinition extends BaseTable
{
    public function query(): Builder
    {
        return SetupConfig::query()
            ->when(
                filled($this->getParameter('tipo')),
                fn ($q) => $q->where(
                    'tipo',
                    $this->getParameter('tipo')
                )
            )
            ->orderBy('nombre');
    }

    protected function searchable(): array
    {
        return [
            'nombre',
            'item_id',
            'tipo',
        ];
    }

    public function columns(): array
    {
        $nombre = (new HtmlColumn(
            label: 'Nombre',
            field: 'nombre',
        ))->formatStateUsing(function ($state, $setupConfig) {
            return '
                <div class="space-y-1">
                    <p class="flex items-start">
                        <span class="break-words font-semibold">'
                            . e($setupConfig->nombre ?? '—') .
                        '</span>
                    </p>

                    <p class="flex items-start gap-1 text-xs">
                        <span class="shrink-0 font-semibold text-slate-800">
                            ITEMID:
                        </span>
                        <span class="break-words text-slate-500">'
                            . e($setupConfig->item_id ?? '—') .
                        '</span>
                    </p>
                </div>
            ';
        });

        $tipoValor = (new HtmlColumn(
            label: 'Valor',
            field: 'tipo',
        ))->formatStateUsing(function ($state, $setupConfig) {
            $tipo_valor = match ((int) $state) {
                                    SetupConfig::TIPO_NUMERO => 'Numérico',
                                    SetupConfig::TIPO_TEXTO => 'Texto',
                                    SetupConfig::TIPO_ARREGLO => 'Arreglo',
                                    default => 'Desconocido',
                                };
            return '
                <div class="space-y-1">
                    <p class="flex items-start">
                        <span class="break-words font-semibold">'
                            . e($tipo_valor) .
                        '</span>
                    </p>

                    <p class="flex items-start mt-1 text-sm">
                        <span class="break-words">'
                            . e($setupConfig->valor) .
                        '</span>
                    </p>
                </div>
            ';
        });

        $definicionValor = (new HtmlColumn(
            label: 'Def. Valor',
            field: 'valor_inicial',
        ))->formatStateUsing(function ($state, $setupConfig) {
            return '
                <div class="space-y-1">
                    <p class="flex items-start gap-1 text-sm">
                        <span class="shrink-0 font-medium text-emerald-800">
                            Inicial:
                        </span>
                        <span class="break-words text-gray-700">'
                            . e($setupConfig->valor_inicial ?? '—') .
                        '</span>
                    </p>

                    <p class="flex items-start gap-1 text-sm">
                        <span class="shrink-0 font-medium text-indigo-800">
                            Mínimo:
                        </span>
                        <span class="break-words text-gray-700">'
                            . e($setupConfig->valor_minimo ?? '—') .
                        '</span>
                    </p>

                    <p class="flex items-start gap-1 text-sm">
                        <span class="shrink-0 font-medium text-orange-800">
                            Máximo:
                        </span>
                        <span class="break-words text-gray-700">'
                            . e($setupConfig->valor_maximo ?? '—') .
                        '</span>
                    </p>
                </div>
            ';
        });

        $acciones = (new ActionsColumn(
            label: 'Acciones',
            field: 'acciones',
        ))->actions([
            (new TableAction('Editar'))
                ->color('yellow')
                ->event('setup-config-editar')
                ->loading('Cargando configuración...'),

            (new TableAction('Eliminar'))
                ->color('red')
                ->event('setup-config-eliminar')
                ->loading('Cargando configuración...'),
        ])->align('right');

        return [
            $nombre,

            new TextColumn(
                label: 'Descripción',
                field: 'descripcion',
            ),

            $tipoValor,

            $definicionValor,

            $acciones,
        ];
    }

    public function valorDefinido(SetupConfig $config): string
    {
        return sprintf(
            'Inicial: %s | Mínimo: %s | Máximo: %s',
            $config->valor_inicial ?? '—',
            $config->valor_minimo ?? '—',
            $config->valor_maximo ?? '—',
        );
    }
}
