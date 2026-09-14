<?php

namespace App\Modules\Finanzas\TarjetaMagnetica\Livewire;

use App\Core\CQRS\HasCommands;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

use App\Modules\Finanzas\TarjetaMagnetica\Livewire\TarjetaMagneticaForm;

class TarjetaMagneticaPage extends Component
{
    use HasCommands;

    public bool $showForm = true;

    public int $tableVersion = 0;

    public bool $showDeleteModal = false;

    public ?int $tarjetaMagneticaDeleteId = null;

    #[On('tarjeta-magnetica-filtros-actualizados')]
    public function actualizarFiltros(): void
    {
        $this->tableVersion++;
    }

    #[On('tarjeta-magnetica-guardada')]
    public function onSaved(): void
    {
        $this->tableVersion++;
    }

    #[On('tarjeta-magnetica-editar')]
    public function editarTarjetaMagnetica(int $id): void
    {
        $this->showForm = true;

        $this->dispatch(
            'tarjeta-magnetica-cargar-edicion',
            id: $id
        )->to(TarjetaMagneticaForm::class);

    }


    #[On('tarjeta-magnetica-eliminar')]
    public function solicitarEliminacion(int $id): void
    {
        $this->tarjetaMagneticaDeleteId = $id;
        $this->showDeleteModal = true;

        $this->dispatch('loading-stop');
    }

    public function cancelarEliminacion(): void
    {
        $this->showDeleteModal = false;
        $this->tarjetaMagneticaDeleteId = null;

        $this->dispatch('livewire:alert', [
            'message' => 'Acción cancelada...',
            'type'    => 'warning',
        ]);
    }

    public function confirmarEliminacion(): void
    {
        if (!$this->tarjetaMagneticaDeleteId) {
            return;
        }

        $this->command(
            'tarjetaMagnetica.delete',
            [
                'id' => $this->tarjetaMagneticaDeleteId,
            ]
        );

        $this->showDeleteModal = false;
        $this->tarjetaMagneticaDeleteId = null;
    }

    #[On('tarjeta-magnetica-eliminada')]
    public function tarjetaMagneticaEliminado(): void
    {
        $this->tableVersion++;

        $this->dispatch('livewire:alert', [
            'message' => 'Tarjeta Magnética eliminada correctamente.',
            'type'    => 'success',
        ]);

        $this->dispatch('reset-form');
    }

    public function toggleForm(): void
    {
        $this->showForm = !$this->showForm;
    }

    public function render()
    {
        return view('modules.finanzas.tarjeta-magnetica.page', [
            'tableVersion' => $this->tableVersion,
        ])->layout('layouts.app_admin');
    }
}
