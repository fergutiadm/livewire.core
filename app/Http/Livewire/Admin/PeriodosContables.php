<?php

namespace App\Http\Livewire\Admin;

use App\Models\Local;
use App\Models\PeriodoContable;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Throwable;

class PeriodosContables extends Component
{
    use WithPagination;

    /* =========================
     *  STATE
     * ========================= */
    public ?int $periodoId = null;

    public string $nombre = '';
    public string $fechaInicio = '';
    public string $fechaFin = '';
    public bool $activo = true;
    public bool $cerrado = false;

    public ?int $periodoIdToDelete = null;
    public bool $confirmingPeriodoDeletion = false;

    public int $localId;
    public $locales;

    public int $perPage = 10;

    public bool $formularioVisible = true;

    /* =========================
     *  INIT
     * ========================= */
    public function mount(?int $localId = null): void
    {
        $this->loadLocales($localId);
        $this->bootFormulario();
    }

    private function bootFormulario(): void
    {
        if (! $this->localId) {
            return;
        }

        $this->nombre = $this->generarNombreSugerido();

        // Obtener último periodo (activo o cerrado)
        $ultimoPeriodo = PeriodoContable::ultimoDeLocal($this->localId);

        if ($ultimoPeriodo) {
            // El nuevo periodo empieza después del último
            $this->fechaInicio = $ultimoPeriodo->fecha_fin->copy()->addDay()->format('Y-m-d');
            $this->fechaFin    = $ultimoPeriodo->fecha_fin->copy()->addDays(6)->format('Y-m-d');
        } else {
            // No hay periodos: iniciar semana actual
            $this->fechaInicio = now()->startOfWeek()->format('Y-m-d');
            $this->fechaFin    = now()->endOfWeek()->format('Y-m-d');
        }
    }


    private function loadLocales(?int $localId): void
    {
        $this->locales = Local::orderBy('nombre')->get();

        if ($this->locales->isEmpty()) {
            return;
        }

        $this->localId = $localId && $this->locales->contains('id', $localId)
            ? $localId
            : $this->locales->first()->id;
    }

    public function updatedLocalId(): void
    {
        $this->resetForm();
        $this->resetPage();
        $this->bootFormulario();
    }

    /* =========================
     *  SAVE
     * ========================= */
    public function save(): void
    {
        $this->validate([
            'localId'     => 'required|exists:locales,id',
            'fechaInicio' => 'required|date',
            'fechaFin'    => 'required|date|after:fechaInicio',
        ], [
            'fechaInicio.required' => 'La Fecha Inicio es obligatoria.',
            'fechaFin.after'       => 'La Fecha Fin debe ser posterior a la Fecha Inicio.',
        ]);

        try {

            $data = [
                'local_id'     => $this->localId,
                'nombre'       => $this->nombre ?: $this->generarNombreSugerido(),
                'fecha_inicio' => $this->fechaInicio,
                'fecha_fin'    => $this->fechaFin,
                'activo'       => true,
                'cerrado'      => false,
            ];

            /* =========================
             *  UPDATE
             * ========================= */
            if ($this->periodoId) {

                $periodo = PeriodoContable::findOrFail($this->periodoId);
                $periodo->update($data);

                $this->dispatch('livewire:alert', [
                    'message' => 'Periodo Contable actualizado correctamente.',
                    'type'    => 'success',
                ]);

            }
            /* =========================
             *  CREATE (cerrar y abrir)
             * ========================= */
            else {

                $periodoActual = PeriodoContable::vigenteActual($this->localId);
                $ultimoPeriodo = $periodoActual ? $periodoActual : PeriodoContable::ultimoDeLocal($this->localId);

                if ($periodoActual) {
                    PeriodoContable::cerrarYAbrirNuevo($periodoActual, $data);
                }elseif ($ultimoPeriodo) {
                    PeriodoContable::cerrarYAbrirNuevo($ultimoPeriodo, $data);
                }else {
                    PeriodoContable::create($data);
                }

                $this->dispatch('livewire:alert', [
                    'message' => 'Periodo Contable creado correctamente.',
                    'type'    => 'success',
                ]);
            }

            $this->resetForm();
            $this->bootFormulario();

        } catch (Throwable $e) {
            Logger('Ocurrió un error', ['Exception'=>$e->getMessage()]);
            $this->dispatch('livewire:alert', [
                'message' => $e->getMessage(),
                'type'    => 'error',
            ]);
        }
        $this->dispatch('periodosActualizados');
    }

    public function localChanged()
    {
        $this->resetPage();
    }

    /* =========================
     *  EDIT / CANCEL / DELETE
     * ========================= */
    #[On('edit')]
    public function edit(int $id): void
    {
        try{
            $periodo = PeriodoContable::findOrFail($id);
        }catch(Throwable $e){
            $this->dispatch('livewire:alert',[
                            'message' => 'Ha ocurrido un error. Contacte al administrador del sistema.',
                            'type' => 'error']
                            );
            Log::error('Error en método edit periodo contable', [
                    'periodo_contable_id'  => $id,
                    'exception'            => $e,
                ]);
            return;
        }

        if ($periodo->cerrado) {
            $this->dispatch('livewire:alert', [
                'message' => 'No se puede editar un período cerrado.',
                'type'    => 'warning',
            ]);
            return;
        }

        $this->periodoId   = $periodo->id;
        $this->nombre      = $periodo->nombre;
        $this->fechaInicio = $periodo->fecha_inicio->format('Y-m-d');
        $this->fechaFin    = $periodo->fecha_fin->format('Y-m-d');
        $this->activo      = $periodo->activo;
        $this->cerrado     = $periodo->cerrado;
    }

    public function cancel(): void
    {
        $this->resetForm();
        $this->bootFormulario();
    }

    #[On('confirmDelete')]
    public function confirmDelete(int $id): void
    {
        $this->periodoIdToDelete = $id;
        $this->confirmingPeriodoDeletion = true;
    }

    public function delete(): void
    {
        if (! $this->periodoIdToDelete) {
            return;
        }

        try{

            PeriodoContable::findOrFail($this->periodoIdToDelete)->delete();

        }catch(Throwable $e){
            $this->dispatch('livewire:alert',[
                            'message' => 'Ha ocurrido un error. Contacte al administrador del sistema.',
                            'type' => 'error']
                            );
            Log::error('Error en método delete periodos', [
                    'periodo_id' => $this->periodoIdToDelete,
                    'exception'   => $e,
                ]);
            return;
        }

        $this->confirmingPeriodoDeletion = false;
        usleep(500000);
        $this->dispatch('periodosActualizadas');

        $this->periodoIdToDelete = null;

        $this->dispatch('livewire:alert', [
            'message' => 'Periodo Contable eliminado correctamente.',
            'type'    => 'success',
        ]);
    }

    /* =========================
     *  HELPERS
     * ========================= */
    private function resetForm(): void
    {
        $this->reset([
            'periodoId',
            'nombre',
            'fechaInicio',
            'fechaFin',
            'activo',
            'cerrado',
        ]);
        $this->resetValidation();
    }

    private function generarNombreSugerido(): string
    {
        if (! $this->localId) {
            return '';
        }

        $year = now()->year;
        $week = now()->weekOfYear;

        do {
            $nombre = "Periodo {$year}-W" . str_pad($week, 2, '0', STR_PAD_LEFT);

            if (! PeriodoContable::where('local_id', $this->localId)->where('nombre', $nombre)->exists()) {
                return $nombre;
            }

            $week++;
        } while ($week <= 53);

        return "Periodo {$year}";
    }

    /* =========================
     *  RENDER
     * ========================= */
    public function render()
    {
        return view('livewire.admin.periodos-contables')
            ->layout('layouts.app_admin');
    }
}
