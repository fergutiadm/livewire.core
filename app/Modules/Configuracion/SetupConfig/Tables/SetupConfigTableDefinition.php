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
            ->orderByDesc('nombre');
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
            new TextColumn(
                label: 'Nombre',
                field: 'nombre',
            ),

            new TextColumn(
                label: 'ItemId',
                field: 'item_id',
            ),

            new TextColumn(
                label: 'Descripción',
                field: 'descripcion',
            ),

            (new TextColumn(
                label: 'Tipo',
                field: 'tipo',
            ))->formatStateUsing(
                fn ($state) => match ((int) $state) {
                    SetupConfig::TIPO_NUMERO => 'Numérico',
                    SetupConfig::TIPO_TEXTO => 'Texto',
                    SetupConfig::TIPO_ARREGLO => 'Arreglo',
                    default => 'Desconocido',
                }
            ),

            new TextColumn(
                label: 'Valor',
                field: 'valor',
            ),

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
