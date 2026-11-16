<?php

namespace App\Modules\Finanzas\Banco\Livewire;

use App\Core\CQRS\HasCommands;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

class BancoPage extends Component
{
    use HasCommands;
    public bool $showForm = true;

    public int $tableVersion = 0;

    public bool $showDeleteModal = false;

    public ?int $bancoId = null;

    public ?int $municipioId = null;
    public ?int $provinciaId = null;

    #[On('banco-filtros-actualizados')]
    public function actualizarFiltros(?int $provinciaId = null, ?int $municipioId = null): void {

        $this->provinciaId = $provinciaId;
        $this->municipioId = $municipioId;

        $this->dispatch('banco-tabla-refrescar', filtros: [
            'provinciaId' => $provinciaId,
            'municipioId' => $municipioId,
        ])->to(BancoTable::class);
    }

    #[On('banco-guardado')]
    public function onSaved(): void
    {
        // Log::info('Listener banco-guardado', ['$this->tableVersion'=>$this->tableVersion]);
        $this->tableVersion++;
    }

    #[On('banco-editar')]
    public function editarBanco(int $id): void
    {
        $this->showForm = true;

        $this->dispatch(
            'banco-cargar-edicion',
            id: $id
        );
    }

    #[On('banco-edicion-cargado')]
    public function bancoEdicionCargado(): void
    {
        $this->dispatch('loading-stop');
    }

    #[On('banco-eliminar')]
    public function solicitarEliminacion(int $id): void
    {
        $this->bancoId = $id;
        $this->showDeleteModal = true;

        $this->dispatch('loading-stop');
    }

    public function cancelarEliminacion(): void
    {
        $this->showDeleteModal = false;
        $this->bancoId = null;

        $this->dispatch('livewire:alert', [
            'message' => 'Acción cancelada...',
            'type'    => 'warning',
        ]);
    }

    public function confirmarEliminacion(): void
    {
        if (!$this->bancoId) {
            return;
        }

        try{
            $this->command(
                'banco.delete',
                [
                    'id' => $this->bancoId,
                ]
            );
        }catch(\Illuminate\Validation\ValidationException $e) {

            $message = collect($e->errors())
                ->flatten()
                ->first();

            $this->dispatch('livewire:alert', [
                'message' => $message ?? 'No se pudo eliminar el banco.',
                'type'    => 'error',
            ]);

            return;
        }finally{
            $this->dispatch('loading-stop');
        }

        $this->dispatch('livewire:alert', [
            'message' => 'Banco Eliminado...',
            'type'    => 'success',
        ]);

        $this->showDeleteModal = false;
        $this->bancoId = null;

        $this->tableVersion++;

        $this->dispatch('reset-form');
    }

    #[On('banco-eliminado')]
    public function bancoEliminado(): void
    {
        $this->tableVersion++;
    }

    public function toggleForm(): void
    {
        $this->showForm = !$this->showForm;
    }

    public function render()
    {
        return view('modules.finanzas.banco.page')
            ->layout('layouts.app_admin');
    }
}