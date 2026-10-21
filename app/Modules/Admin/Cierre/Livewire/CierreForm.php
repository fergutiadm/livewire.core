<?php

namespace App\Modules\Admin\Cierre\Livewire;

use App\Core\CQRS\HasCommands;
use App\Models\Denominacion;
use App\Core\Context\LocalContext;
use App\Core\Context\PeriodoContext;
use App\Models\Gasto;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CierreForm extends Component
{
    use HasCommands;

    public float $montoInicialDefinido = 0.00;
    public array $cantidades = [];
    public array $denominaciones = [];

    public function mount(): void
    {
        $localId = app(LocalContext::class)->id();
        $periodoId = app(PeriodoContext::class)->id();

        $totalVentas = 5000.00; // Temporal provisional

        $totalGastos = Gasto::where('local_id', $localId)
            ->where('periodo_contable_id', $periodoId)
            ->where('chequeado', true)
            ->get()
            ->sum(fn($g) => (float)$g->importe_calculado);

        $this->montoInicialDefinido = max(0, $totalVentas - $totalGastos);
        $this->cargarDenominaciones();
    }

    public function cargarDenominaciones(): void
    {
        $this->denominaciones = Denominacion::where('activa', true)
            ->orderByDesc('valor')
            ->get()
            ->toArray();

        foreach ($this->denominaciones as $den) {
            $this->cantidades[$den['id']] = 0;
        }
    }

    public function getMontoDesglosadoTotalProperty(): float
    {
        $total = 0;
        foreach ($this->denominaciones as $den) {
            $qty = (int) ($this->cantidades[$den['id']] ?? 0);
            $total += $qty * (float) $den['valor'];
        }
        return $total;
    }

    public function save(): void
    {
        $totalDesglosado = $this->montoDesglosadoTotal;

        if (number_format($totalDesglosado, 2, '.', '') !== number_format($this->montoInicialDefinido, 2, '.', '')) {
            $this->dispatch('livewire:alert', [
                'type' => 'error',
                'message' => 'El total físico no coincide con el monto esperado en caja.'
            ]);
            return;
        }

        $this->command('cierre.create', [
            'local_id' => app(LocalContext::class)->id(),
            'periodo_contable_id' => app(PeriodoContext::class)->id(),
            'operador_id' => Auth::id(),
            'monto_inicial_definido' => $this->montoInicialDefinido,
            'monto_desglosado_total' => $totalDesglosado,
            'fecha' => now()->format('Y-m-d'),
            'cantidades' => $this->cantidades,
            'denominaciones' => $this->denominaciones
        ]);

        $this->dispatch('livewire:alert', ['type' => 'success', 'message' => 'Cierre de caja procesado con éxito']);
        $this->dispatch('cierre-guardado');
        $this->reset(['cantidades']);
        $this->cargarDenominaciones();
    }

    public function render()
    {
        return view('modules.admin.cierre.form');
    }
}