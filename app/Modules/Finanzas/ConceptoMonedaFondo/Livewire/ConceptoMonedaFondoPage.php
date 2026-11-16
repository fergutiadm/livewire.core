<?php

namespace App\Modules\Finanzas\ConceptoMonedaFondo\Livewire;

use App\Core\CQRS\HasCommands;
use Livewire\Attributes\On;
use Livewire\Component;

class ConceptoMonedaFondoPage extends Component
{
    use HasCommands;
    public bool $showForm = true;

    public int $tableVersion = 0;

    public bool $showDeleteModal = false;

    public ?int $conceptoMonedaFondoId = null;

    #[On('concepto-moneda-fondo-filtros-actualizados')]
    public function actualizarFiltros(): void {

        $this->tableVersion++;

        // Log::info('EVENTO ConceptoMonedaFondo FILTRO ACTUALIZADOS', [
            //             'conceptoMonedaFondoId' => $conceptoMonedaFondoId,
            //         ]);
    }

    #[On('concepto-moneda-fondo-guardado')]
    public function onSaved(): void
    {
        // Log::info('Listener concepto-moneda-fondo-guardado', ['$this->tableVersion'=>$this->tableVersion]);
        $this->tableVersion++;
    }

    #[On('concepto-moneda-fondo-editar')]
    public function editarConceptoMonedaFondo(int $id): void
    {
        $this->showForm = true;

        $this->dispatch(
            'concepto-moneda-fondo-cargar-edicion',
            id: $id
        );
    }

    #[On('concepto-moneda-fondo-edicion-cargado')]
    public function conceptoMonedaFondoEdicionCargado(): void
    {
        $this->dispatch('loading-stop');
    }

    #[On('concepto-moneda-fondo-eliminar')]
    public function solicitarEliminacion(int $id): void
    {
        $this->conceptoMonedaFondoId = $id;
        $this->showDeleteModal = true;

        $this->dispatch('loading-stop');
    }

    public function cancelarEliminacion(): void
    {
        $this->showDeleteModal = false;
        $this->conceptoMonedaFondoId = null;

        $this->dispatch('livewire:alert', [
            'message' => 'Acción cancelada...',
            'type'    => 'warning',
        ]);
    }

    public function confirmarEliminacion(): void
    {
        if (!$this->conceptoMonedaFondoId) {
            return;
        }

        try{
            $this->command(
                'conceptoMonedaFondo.delete',
                [
                    'id' => $this->conceptoMonedaFondoId,
                ]
            );
        }catch(\Illuminate\Validation\ValidationException $e) {

            $message = collect($e->errors())
                ->flatten()
                ->first();

            $this->dispatch('livewire:alert', [
                'message' => $message ?? 'No se pudo eliminar el concepto.',
                'type'    => 'error',
            ]);

            return;
        }finally{
            $this->dispatch('loading-stop');
        }

        $this->dispatch('livewire:alert', [
            'message' => 'Concepto Eliminado...',
            'type'    => 'success',
        ]);

        $this->showDeleteModal = false;
        $this->conceptoMonedaFondoId = null;

        $this->tableVersion++;

        $this->dispatch('reset-form');
    }

    #[On('concepto-moneda-fondo-eliminado')]
    public function conceptoMonedaFondoEliminado(): void
    {
        $this->tableVersion++;
    }

    public function toggleForm(): void
    {
        $this->showForm = !$this->showForm;
    }

    public function render()
    {
        return view('modules.finanzas.concepto-moneda-fondo.page')
            ->layout('layouts.app_admin');;
    }
}