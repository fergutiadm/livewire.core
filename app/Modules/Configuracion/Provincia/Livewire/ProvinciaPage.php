<?php

namespace App\Modules\Configuracion\Provincia\Livewire;

use App\Core\CQRS\HasCommands;
use Livewire\Attributes\On;
use Livewire\Component;

class ProvinciaPage extends Component
{
    use HasCommands;
    public bool $showForm = true;

    public int $tableVersion = 0;

    public bool $showDeleteModal = false;

    public ?int $provinciaId = null;

    #[On('provincia-filtros-actualizados')]
    public function actualizarFiltros(): void {

        $this->tableVersion++;

        // Log::info('EVENTO provincia FILTRO ACTUALIZADOS', [
        //             'provinciaId' => $provinciaId,
        //         ]);
    }

    #[On('provincia-guardado')]
    public function onSaved(): void
    {
        // Log::info('Listener provincia-guardado', ['$this->tableVersion'=>$this->tableVersion]);
        $this->tableVersion++;
    }

    #[On('provincia-editar')]
    public function editarProvincia(int $id): void
    {
        $this->showForm = true;

        $this->dispatch(
            'provincia-cargar-edicion',
            id: $id
        );
    }

    #[On('provincia-edicion-cargado')]
    public function provinciaEdicionCargado(): void
    {
        $this->dispatch('loading-stop');
    }

    #[On('provincia-eliminar')]
    public function solicitarEliminacion(int $id): void
    {
        $this->provinciaId = $id;
        $this->showDeleteModal = true;

        $this->dispatch('loading-stop');
    }

    public function cancelarEliminacion(): void
    {
        $this->showDeleteModal = false;
        $this->provinciaId = null;

        $this->dispatch('livewire:alert', [
            'message' => 'Acción cancelada...',
            'type'    => 'warning',
        ]);
    }

    public function confirmarEliminacion(): void
    {
        if (!$this->provinciaId) {
            return;
        }

        try{
            $this->command(
                'provincia.delete',
                [
                    'id' => $this->provinciaId,
                ]
            );
        }catch(\Illuminate\Validation\ValidationException $e) {

            $message = collect($e->errors())
                ->flatten()
                ->first();

            $this->dispatch('livewire:alert', [
                'message' => $message ?? 'No se pudo eliminar la provincia.',
                'type'    => 'error',
            ]);

            return;
        }finally{
            $this->dispatch('loading-stop');
        }

        $this->dispatch('livewire:alert', [
            'message' => 'Provincia Eliminada...',
            'type'    => 'success',
        ]);

        $this->showDeleteModal = false;
        $this->provinciaId = null;

        $this->tableVersion++;

        $this->dispatch('reset-form');
    }

    #[On('provincia-eliminado')]
    public function provinciaEliminado(): void
    {
        $this->tableVersion++;
    }

    public function toggleForm(): void
    {
        $this->showForm = !$this->showForm;
    }

    public function render()
    {
        return view('modules.configuracion.provincia.page')
            ->layout('layouts.app_admin');
    }
}