<?php

namespace App\Modules\Finanzas\Banco\Livewire;

use App\Core\Tables\TableComponent;
use App\Models\Banco;
use App\Models\Municipio;
use App\Models\Provincia;
use App\Modules\Configuracion\SetupConfig\Services\SetupConfigService;
use App\Modules\Finanzas\Banco\Actions\ToggleBancoActivoAction;
use App\Modules\Finanzas\Banco\Actions\ToggleBancoPorDefectoAction;
use Livewire\Attributes\On;

//use App\Modules\Finanzas\Banco\Tables\BancoTableDefinition;

class BancoTable extends TableComponent
{
    public ?int $provinciaId = null;
    public ?int $municipioId = null;

    protected bool $stopLoadingAfterRender = false;

    public function mount(): void
    {
        $provincia_default = app(SetupConfigService::class)->get('provincia_default');
        $this->provinciaId = Provincia::query()->where('slug', $provincia_default)->value('id')??null;

        $municipio_default = app(SetupConfigService::class)->get('municipio_default');
        $this->municipioId = Municipio::query()->where('slug', $municipio_default)->value('id')??null;
    }

    #[On('banco-tabla-refrescar')]
    public function mapearFiltrosYRefrescar(array $filtros): void
    {
        $this->provinciaId = $filtros['provinciaId'] ?? null;

        $this->municipioId = $filtros['municipioId'] ?? null;

        $this->stopLoadingAfterRender = true;
    }

    public function toggleActivo(int $bancoId): void
    {
        $banco = Banco::findOrFail($bancoId);

        app(ToggleBancoActivoAction::class)->execute($banco);
    }

    public function togglePorDefecto(int $bancoId): void
    {
        $banco = Banco::findOrFail($bancoId);

        app(ToggleBancoPorDefectoAction::class)->execute($banco);
    }

    public function rendered(): void
    {
        if ($this->stopLoadingAfterRender) {
            $this->stopLoadingAfterRender = false;
            $this->dispatch('loading-stop');
        }
    }

    protected function tableParameters(): array
    {
        return [
            'provinciaId' => $this->provinciaId,
            'municipioId' => $this->municipioId,
        ];
    }

    #[On('banco-limpiar-filtros')]
    public function limpiarFiltros(): void
    {
        $this->reset(
            'provinciaId',
            'municipioId',
        );
    }

    protected function tableClass(): string
    {
        return \App\Modules\finanzas\Banco\Tables\BancoTableDefinition::class;
    }
}