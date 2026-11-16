<?php

namespace App\Modules\Finanzas\BancoSucursal\Livewire;

use App\Core\CQRS\HasCommands;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

class BancoSucursalPage extends Component
{
    use HasCommands;
    public bool $showForm = true;

    public int $tableVersion = 0;

    public bool $showDeleteModal = false;

    public ?int $sucursalId = null;

    public ?int $municipioId = null;
    public ?int $provinciaId = null;

    #[On('banco-sucursal-filtros-actualizados')]
    public function actualizarFiltros(?int $provinciaId = null, ?int $municipioId = null): void {

        $this->provinciaId = $provinciaId;
        $this->municipioId = $municipioId;

        $this->dispatch('banco-sucursal-tabla-refrescar', filtros: [
            'provinciaId' => $provinciaId,
            'municipioId' => $municipioId,
        ])->to(BancoSucursalTable::class);
    }

    #[On('banco-sucursal-guardado')]
    public function onSaved(): void
    {
        // Log::info('Listener banco-sucursal-guardado', ['$this->tableVersion'=>$this->tableVersion]);
        $this->tableVersion++;
    }

    #[On('banco-sucursal-editar')]
    public function editarBancoSucursal(int $id): void
    {
        $this->showForm = true;

        $this->dispatch(
            'banco-sucursal-cargar-edicion',
            id: $id
        );
    }

    #[On('banco-sucursal-edicion-cargado')]
    public function bancoSucursalEdicionCargado(): void
    {
        $this->dispatch('loading-stop');
    }

    #[On('banco-sucursal-eliminar')]
    public function solicitarEliminacion(int $id): void
    {
        $this->sucursalId = $id;
        $this->showDeleteModal = true;

        $this->dispatch('loading-stop');
    }

    public function cancelarEliminacion(): void
    {
        $this->showDeleteModal = false;
        $this->sucursalId = null;

        $this->dispatch('livewire:alert', [
            'message' => 'Acción cancelada...',
            'type'    => 'warning',
        ]);
    }

    public function confirmarEliminacion(): void
    {
        if (!$this->sucursalId) {
            return;
        }

        try{
            $this->command(
                'bancoSucursal.delete',
                [
                    'id' => $this->sucursalId,
                ]
            );
        }catch(\Illuminate\Validation\ValidationException $e) {

            $message = collect($e->errors())
                ->flatten()
                ->first();

            $this->dispatch('livewire:alert', [
                'message' => $message ?? 'No se pudo eliminar la sucursal.',
                'type'    => 'error',
            ]);

            return;
        }finally{
            $this->dispatch('loading-stop');
        }

        $this->dispatch('livewire:alert', [
            'message' => 'Sucursal Eliminada...',
            'type'    => 'success',
        ]);

        $this->showDeleteModal = false;
        $this->sucursalId = null;

        $this->tableVersion++;

        $this->dispatch('reset-form');
    }

    #[On('banco-sucursal-eliminado')]
    public function bancoSucursalEliminado(): void
    {
        $this->tableVersion++;
    }

    public function toggleForm(): void
    {
        $this->showForm = !$this->showForm;
    }

    public function render()
    {
        return view('modules.finanzas.banco-sucursal.page')
            ->layout('layouts.app_admin');
    }
}