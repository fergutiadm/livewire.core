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
