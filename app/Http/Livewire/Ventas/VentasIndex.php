<?php

namespace App\Http\Livewire\Ventas;

use App\Core\CQRS\HasCommands;
use Livewire\Attributes\On;
use Livewire\Component;

class VentasIndex extends Component
{
    use HasCommands;

    public bool $showForm = true;

    public int $tableVersion = 0;

    public bool $showDeleteModal = false;

    public bool $verDetallesVenta = false;

    public ?string $fechaOperacion = null;

    public array $detallesVentaVisibles = [];

    public function mount(): void
    {
        $this->fechaOperacion = now()->format('Y-m-d');
    }

    #[On('fecha-operacion-context-changed')]
    public function actualizarFechaOperacion(string $fechaOperacion): void
    {
        $this->fechaOperacion = $fechaOperacion;

        // Al cambiar de fecha, cerramos los detalles abiertos.
        $this->detallesVentaVisibles = [];
        $this->verDetallesVenta = false;
    }

    public function toggleTodosDetallesVenta(array $idsVentas): void
    {
        if ($this->verDetallesVenta) {
            $this->detallesVentaVisibles = [];
            $this->verDetallesVenta = false;

            return;
        }

        $this->detallesVentaVisibles = array_values(
            array_unique($idsVentas)
        );

        $this->verDetallesVenta = true;
    }

    public function toggleDetalleVenta(
        int $ventaId,
        array $idsVentas
    ): void {
        if (in_array($ventaId, $this->detallesVentaVisibles)) {
            $this->detallesVentaVisibles = array_values(
                array_diff($this->detallesVentaVisibles, [$ventaId])
            );
        } else {
            $this->detallesVentaVisibles[] = $ventaId;

            $this->detallesVentaVisibles = array_values(
                array_unique($this->detallesVentaVisibles)
            );
        }

        $this->verDetallesVenta = !empty($idsVentas)
            && count(array_intersect(
                $idsVentas,
                $this->detallesVentaVisibles
            )) === count($idsVentas);
    }

    public function render()
    {
        return view('livewire.ventas.ventas-index')
            ->layout('layouts.app');
    }
}