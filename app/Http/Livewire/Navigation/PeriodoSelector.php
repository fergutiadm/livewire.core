<?php

namespace App\Http\Livewire\Navigation;

use App\Core\Context\LocalContext;
use App\Core\Context\PeriodoContext;
use App\Models\PeriodoContable;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

class PeriodoSelector extends Component
{
    public bool $showModal = false;
    public ?int $periodoId = null;
    public $periodos;
    public bool $periodoSelectedActivo = false;

    public function mount(PeriodoContext $periodoContext): void
    {
        $this->periodoId = $periodoContext->id();
        
        $this->cargarPeriodos();
    }

    /**
     * OPTIMIZACIÓN EXTRA: Aislamos la carga de periodos para no repetir código
     */
    private function cargarPeriodos(): void
    {
        $localId = app(LocalContext::class)->id();
        $this->periodos = PeriodoContable::when(
                filled($localId),
                fn ($q) => $q->where('local_id', $localId)
            )
            ->orderByDesc('activo')
            ->orderByDesc('fecha_inicio')
            ->get();

        $this->periodoSelectedActivo = $this->periodos->firstWhere('id', $this->periodoId)?->activo ?? false;        
    }

    /**
     * ESCUCHA EL CAMBIO GLOBAL: Este listener reacciona cuando GastoPage 
     * o el listener de Login modifican el periodo en el servidor.
     */
    #[On('periodo-context-changed')]
    public function actualizarPeriodoActual(int $periodoId): void
    {
        // 1. Volvemos a cargar los periodos porque el local pudo haber cambiado
        $this->cargarPeriodos();
        
        // 2. Sincronizamos el ID para que la vista HTML marque el correcto
        $this->periodoId = $periodoId;

        Log::info('periodo-context-changed',
            [
                '$periodoId' => $periodoId
            ]
        );

        $this->dispatch('fecha-operacion-seleccion');

    }

    public function openModal(): void
    {
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
    }

    public function seleccionarPeriodo(int $periodoId): void
    {
        $periodo = $this->periodos->firstWhere('id', $periodoId);

        if (!$periodo) {
            return;
        }

        app(PeriodoContext::class)->set($periodoId);
        $this->periodoId = $periodoId;
        $this->showModal = false;
        
        // Notificamos al resto de la aplicación (como a GastoPage)
        $this->dispatch(
            'periodo-context-changed',
            periodoId: $periodoId
        );

        $this->dispatch('fecha-operacion-seleccion');
    }

    public function render()
    {
        $periodoActual = $this->periodos->firstWhere('id', $this->periodoId);

        return view('navigation.periodo-selector', [
            'fecha_inicio' => $periodoActual->fecha_inicio,
            'fecha_fin' => $periodoActual->fecha_fin,
        ]);
    }
}
