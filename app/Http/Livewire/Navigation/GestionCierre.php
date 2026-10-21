<?php

namespace App\Http\Livewire\Navigation;

use App\Core\Context\PeriodoContext;
use Livewire\Attributes\On;
use Livewire\Component;

class GestionCierre extends Component
{
    public bool $showModal = false;
    public ?int $periodoId = null;

    public function mount(PeriodoContext $periodoContext): void
    {
        // $this->periodoId = $periodoContext->id();
    }

    /**
     * ESCUCHA EL CAMBIO GLOBAL: Este listener reacciona cuando GastoPage 
     * o el listener de Login modifican el periodo en el servidor.
     */
    // #[On('periodo-context-changed')]
    // public function actualizarPeriodoActual(int $periodoId): void
    // {
    //     $this->periodoId = $periodoId;

    // }

    public function openModal(): void
    {
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
    }

    public function render()
    {
        // $periodoActual = $this->periodos->firstWhere('id', $this->periodoId);

        return view('navigation.gestion-cierre');
    }
}
