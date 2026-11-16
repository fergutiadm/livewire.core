<?php

namespace App\Modules\Finanzas\BancoSucursal\Livewire;

use App\Core\Tables\TableComponent;
use App\Models\BancoSucursal;
use App\Models\Municipio;
use App\Models\Provincia;
use App\Modules\Configuracion\SetupConfig\Services\SetupConfigService;
use App\Modules\Finanzas\BancoSucursal\Actions\ToggleBancoSucursalActivaAction;
use Livewire\Attributes\On;

//use App\Modules\Finanzas\Banco\Tables\BancoTableDefinition;

class BancoSucursalTable extends TableComponent
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

    #[On('banco-sucursal-tabla-refrescar')]
    public function mapearFiltrosYRefrescar(array $filtros): void
    {
        $this->provinciaId = $filtros['provinciaId'] ?? null;
        $this->municipioId = $filtros['municipioId'] ?? null;

        $this->stopLoadingAfterRender = true;
    }

    public function toggleActiva(int $sucursalId): void
    {
        $sucursal = BancoSucursal::findOrFail($sucursalId);

        app(ToggleBancoSucursalActivaAction::class)->execute($sucursal);
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

    #[On('banco-sucursal-limpiar-filtros')]
    public function limpiarFiltros(): void
    {
        $this->reset(
            'provinciaId',
            'municipioId',
        );
    }

    protected function tableClass(): string
    {
        return \App\Modules\finanzas\BancoSucursal\Tables\BancoSucursalTableDefinition::class;
    }
}