<?php

namespace App\Modules\Finanzas\CuentaBancaria\Livewire;

use App\Core\Tables\TableComponent;
use App\Models\CuentaBancaria;
use App\Models\Municipio;
use App\Models\Provincia;
use App\Modules\Configuracion\SetupConfig\Services\SetupConfigService;
use App\Modules\Finanzas\CuentaBancaria\Actions\ToggleCuentaBancariaActivaAction;
use App\Services\MonedaService;
use Livewire\Attributes\On;

//use App\Modules\Finanzas\CuentaBancaria\Tables\CuentaBancariaTableDefinition;

class CuentaBancariaTable extends TableComponent
{
    public ?int $provinciaId = null;
    public ?int $municipioId = null;
    public ?int $sucursalId = null;
    public ?int $monedaId = null;

    protected bool $stopLoadingAfterRender = false;

    public function mount(): void
    {
        $provincia_default = app(SetupConfigService::class)->get('provincia_default');
        $this->provinciaId = Provincia::query()->where('slug', $provincia_default)->value('id')??null;

        $municipio_default = app(SetupConfigService::class)->get('municipio_default');
        $this->municipioId = Municipio::query()->where('slug', $municipio_default)->value('id')??null;

        $monedaPrincipal = app(MonedaService::class)->principal();
        $this->monedaId = $monedaPrincipal['id'];
    }

    #[On('cuenta-bancaria-tabla-refrescar')]
    public function mapearFiltrosYRefrescar(array $filtros): void
    {
        $this->provinciaId = $filtros['provinciaId'] ?? null;
        $this->municipioId = $filtros['municipioId'] ?? null;
        $this->sucursalId = $filtros['sucursalId'] ?? null;
        $this->monedaId = $filtros['monedaId'] ?? null;

        $this->stopLoadingAfterRender = true;
    }

    public function toggleActivo(int $cuentaBancariaId): void
    {
        $cuentaBancaria = CuentaBancaria::findOrFail($cuentaBancariaId);

        app(ToggleCuentaBancariaActivaAction::class)->execute($cuentaBancaria);
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
            'sucursalId' => $this->sucursalId,
            'monedaId' => $this->monedaId,
        ];
    }

    #[On('cuenta-bancaria-limpiar-filtros')]
    public function limpiarFiltros(): void
    {
        $this->reset(
            'provinciaId',
            'municipioId',
            'sucursalId',
            'monedaId',
        );
    }

    protected function tableClass(): string
    {
        return \App\Modules\finanzas\CuentaBancaria\Tables\CuentaBancariaTableDefinition::class;
    }
}