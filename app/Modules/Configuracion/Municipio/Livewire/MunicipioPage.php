<?php

namespace App\Modules\Configuracion\Municipio\Livewire;

use App\Core\CQRS\HasCommands;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

class MunicipioPage extends Component
{
    use HasCommands;
    public bool $showForm = true;

    public int $tableVersion = 0;

    public bool $showDeleteModal = false;

    public ?int $municipioId = null;
    public ?int $provinciaId = null;

    #[On('municipio-filtros-actualizados')]
    public function actualizarFiltros(?int $provinciaId = null): void {

        $this->provinciaId = $provinciaId;

        $this->dispatch('municipio-tabla-refrescar', filtros: [
            'provinciaId' => $provinciaId,
        ])->to(MunicipioTable::class);
    }

    #[On('municipio-guardado')]
    public function onSaved(): void
    {
        // Log::info('Listener municipio-guardado', ['$this->tableVersion'=>$this->tableVersion]);
        $this->tableVersion++;
    }

    #[On('municipio-editar')]
    public function editarMunicipio(int $id): void
    {
        $this->showForm = true;

        $this->dispatch(
            'municipio-cargar-edicion',
            id: $id
        );
    }

    #[On('municipio-edicion-cargado')]
    public function municipioEdicionCargado(): void
    {
        $this->dispatch('loading-stop');
    }

    #[On('municipio-eliminar')]
    public function solicitarEliminacion(int $id): void
    {
        $this->municipioId = $id;
        $this->showDeleteModal = true;

        $this->dispatch('loading-stop');
    }

    public function cancelarEliminacion(): void
    {
        $this->showDeleteModal = false;
        $this->municipioId = null;

        $this->dispatch('livewire:alert', [
            'message' => 'Acción cancelada...',
            'type'    => 'warning',
        ]);
    }

    public function confirmarEliminacion(): void
    {
        if (!$this->municipioId) {
            return;
        }

        try{
            $this->command(
                'municipio.delete',
                [
                    'id' => $this->municipioId,
                ]
            );
        }catch(\Illuminate\Validation\ValidationException $e) {

            $message = collect($e->errors())
                ->flatten()
                ->first();

            $this->dispatch('livewire:alert', [
                'message' => $message ?? 'No se pudo eliminar el municipio.',
                'type'    => 'error',
            ]);

            return;
        }finally{
            $this->dispatch('loading-stop');
        }

        $this->dispatch('livewire:alert', [
            'message' => 'Municipio Eliminada...',
            'type'    => 'success',
        ]);

        $this->showDeleteModal = false;
        $this->municipioId = null;

        $this->tableVersion++;

        $this->dispatch('reset-form');
    }

    #[On('municipio-eliminado')]
    public function municipioEliminado(): void
    {
        $this->tableVersion++;
    }

    public function toggleForm(): void
    {
        $this->showForm = !$this->showForm;
    }

    public function render()
    {
        return view('modules.configuracion.municipio.page')
            ->layout('layouts.app_admin');
    }
}