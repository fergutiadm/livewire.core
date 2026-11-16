<?php

namespace App\Modules\Finanzas\CuentaBancaria\Livewire;

use App\Core\CQRS\HasCommands;
use Livewire\Attributes\On;
use Livewire\Component;

class CuentaBancariaPage extends Component
{
    use HasCommands;
    public bool $showForm = true;

    public int $tableVersion = 0;

    public bool $showDeleteModal = false;

    public ?int $cuentaBancariaId = null;

    public ?int $municipioId = null;
    public ?int $provinciaId = null;
    public ?int $monedaId = null;

    #[On('cuenta-bancaria-filtros-actualizados')]
    public function actualizarFiltros(?int $provinciaId = null, ?int $municipioId = null, ?int $monedaId = null): void {

        $this->provinciaId = $provinciaId;
        $this->municipioId = $municipioId;
        $this->monedaId = $monedaId;

        $this->dispatch('cuenta-bancaria-tabla-refrescar', filtros: [
            'provinciaId' => $provinciaId,
            'municipioId' => $municipioId,
            'monedaId' => $monedaId,
        ])->to(CuentaBancariaTable::class);
    }

    #[On('cuenta-bancaria-guardado')]
    public function onSaved(): void
    {
        // Log::info('Listener cuenta-bancaria-guardado', ['$this->tableVersion'=>$this->tableVersion]);
        $this->tableVersion++;
    }

    #[On('cuenta-bancaria-editar')]
    public function editarCuentaBancaria(int $id): void
    {
        $this->showForm = true;

        $this->dispatch(
            'cuenta-bancaria-cargar-edicion',
            id: $id
        );
    }

    #[On('cuenta-bancaria-edicion-cargado')]
    public function cuentaBancariaEdicionCargado(): void
    {
        $this->dispatch('loading-stop');
    }

    #[On('cuenta-bancaria-eliminar')]
    public function solicitarEliminacion(int $id): void
    {
        $this->cuentaBancariaId = $id;
        $this->showDeleteModal = true;

        $this->dispatch('loading-stop');
    }

    public function cancelarEliminacion(): void
    {
        $this->showDeleteModal = false;
        $this->cuentaBancariaId = null;

        $this->dispatch('livewire:alert', [
            'message' => 'Acción cancelada...',
            'type'    => 'warning',
        ]);
    }

    public function confirmarEliminacion(): void
    {
        if (!$this->cuentaBancariaId) {
            return;
        }

        try{
            $this->command(
                'cuentaBancaria.delete',
                [
                    'id' => $this->cuentaBancariaId,
                ]
            );
        }catch(\Illuminate\Validation\ValidationException $e) {

            $message = collect($e->errors())
                ->flatten()
                ->first();

            $this->dispatch('livewire:alert', [
                'message' => $message ?? 'No se pudo eliminar la cuenta bancaria.',
                'type'    => 'error',
            ]);

            return;
        }finally{
            $this->dispatch('loading-stop');
        }

        $this->dispatch('livewire:alert', [
            'message' => 'Cuenta bancaria Eliminado...',
            'type'    => 'success',
        ]);

        $this->showDeleteModal = false;
        $this->cuentaBancariaId = null;

        $this->tableVersion++;

        $this->dispatch('reset-form');
    }

    #[On('cuenta-bancaria-eliminado')]
    public function cuentaBancariaEliminado(): void
    {
        $this->tableVersion++;
    }

    public function toggleForm(): void
    {
        $this->showForm = !$this->showForm;
    }

    public function render()
    {
        return view('modules.finanzas.cuenta-bancaria.page')
                ->layout('layouts.app_admin');
    }
}