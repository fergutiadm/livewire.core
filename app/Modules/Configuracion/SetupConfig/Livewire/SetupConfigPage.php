<?php

namespace App\Modules\Configuracion\SetupConfig\Livewire;

use App\Core\CQRS\HasCommands;
use Livewire\Attributes\On;
use Livewire\Component;

class SetupConfigPage extends Component
{
    use HasCommands;

    public bool $showForm = true;
    public int $tableVersion = 0;
    public bool $showDeleteModal = false;
    public ?int $setupConfigDeleteId = null;

    #[On('setup-config-filtros-actualizados')]
    #[On('setup-config-guardado')]
    public function actualizarTabla(): void
    {
        $this->tableVersion++;
    }

    #[On('setup-config-editar')]
    public function editarSetupConfig(int $id): void
    {
        $this->showForm = true;

        $this->dispatch('setup-config-cargar-edicion', id: $id);
    }

    #[On('setup-config-edicion-cargado')]
    public function setupConfigEdicionCargada(
        ?int $setupConfigId = null
    ): void {
        $this->dispatch('loading-stop');
    }

    #[On('setup-config-eliminar')]
    public function solicitarEliminacion(int $id): void
    {
        $this->setupConfigDeleteId = $id;
        $this->showDeleteModal = true;

        $this->dispatch('loading-stop');
    }

    public function cancelarEliminacion(): void
    {
        $this->showDeleteModal = false;
        $this->setupConfigDeleteId = null;

        $this->dispatch('alert', [
            'type' => 'warning',
            'message' => 'Eliminación cancelada.',
        ]);
    }

    public function confirmarEliminacion(): void
    {
        if ($this->setupConfigDeleteId === null) {
            return;
        }

        $this->command('setupConfig.delete', [
            'id' => $this->setupConfigDeleteId,
        ]);

        $this->showDeleteModal = false;
        $this->setupConfigDeleteId = null;
        $this->tableVersion++;

        $this->dispatch('reset-form');

        $this->dispatch('alert', [
            'type' => 'success',
            'message' => 'Configuración eliminada correctamente.',
        ]);
    }

    public function toggleForm(): void
    {
        $this->showForm = !$this->showForm;
    }

    public function render()
    {
        return view('modules.configuracion.setup-config.page')
            ->layout('layouts.app_admin');
    }
}
