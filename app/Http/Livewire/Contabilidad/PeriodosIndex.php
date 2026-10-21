<?php

namespace App\Http\Livewire\Contabilidad;

use App\Core\Context\LocalContext;
use App\Models\PeriodoContable;
use Livewire\Component;

class PeriodosIndex extends Component
{
    public $periodosContables;

    public function mount()
    {
        $localId = app(LocalContext::class)->id();
        $this->periodosContables = PeriodoContable::query()
            ->DeLocal($localId)
            ->orderByDesc('activo')
            ->orderByDesc('fecha_inicio')
            ->get();
    }

    public function render()
    {
        return view('livewire.contabilidad.periodos-index')
            ->layout('layouts.app');;
    }
}
