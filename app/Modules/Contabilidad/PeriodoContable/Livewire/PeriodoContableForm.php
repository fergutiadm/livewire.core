<?php

namespace App\Modules\Contabilidad\PeriodoContable\Livewire;

use App\Core\CQRS\HasCommands;
use App\Models\Local;
use App\Models\PeriodoContable;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

class PeriodoContableForm extends Component
{
    use HasCommands;

    public ?int $periodoContableId = null;
    public ?int $localId = null;

    public string $nombre = '';

    public string $fecha_inicio = '';

    public string $fecha_fin = '';

    public bool $activo = true;

    public bool $cerrado = false;

    protected $rules = [
        'nombre' => 'required|string|max:255',

        'localId' => 'required|exists:locales,id',

        'fecha_inicio' => 'required|date',
        'fecha_fin'    => 'required|date|after:fechaInicio',
    ];

    protected function messages(): array
    {
        return [
            'nombre.required' =>
                'El Nombre es obligatorio',

            'localId.required' =>
                'El Local es obligatorio',

            'localId.exists' =>
                'El Local seleccionado no es válido',

            'fecha_inicio.required' => 'La Fecha Inicio es obligatoria.',
            'fecha_fin.after'       => 'La Fecha Fin debe ser posterior a Fecha Inicio.',
        ];
    }

    public function mount(): void
    {
        $this->localId = Local::orderBy('nombre')->value('id');

        if ($this->localId) {
            $this->bootFormulario();
        }
    }


    private function bootFormulario(): void
    {
        if (! $this->localId) {
        return;
        }

        $this->nombre = $this->generarNombreSugerido();

        // Obtener último periodo, activo o cerrado
        $ultimoPeriodo = PeriodoContable::ultimoDeLocal($this->localId);

        if ($ultimoPeriodo) {

            // El nuevo periodo comienza después del último
            $this->fecha_inicio = $ultimoPeriodo->fecha_fin
                ->copy()
                ->addDay()
                ->format('Y-m-d');

            $this->fecha_fin = $ultimoPeriodo->fecha_fin
                ->copy()
                ->addDays(6)
                ->format('Y-m-d');

                Log::info('bootFormulario - UltimoPeriodo', [
                    'fecha_inicio' => $this->fecha_inicio,
                    'fecha_fin' => $this->fecha_fin,
                ]);
        } else {

            // No hay periodos: iniciar semana actual
            $this->fecha_inicio = now()
                ->startOfWeek()
                ->format('Y-m-d');

            $this->fecha_fin = now()
                ->endOfWeek()
                ->format('Y-m-d');

            Log::info('bootFormulario - NO HAY Periodo', [
                    'fecha_inicio' => $this->fecha_inicio,
                    'fecha_fin' => $this->fecha_fin,
                ]);

        }

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


    #[On('periodo-contable-cargar-edicion')]
    public function edit(int $id): void
    {
        $periodoContable = PeriodoContable::findOrFail($id);

        $this->periodoContableId = $periodoContable->id;

        $this->localId = $periodoContable->local_id;

        $this->nombre = $periodoContable->nombre;

        $this->activo = $periodoContable->activo ?? false;

        $this->cerrado = $periodoContable->cerrado ?? false;

        $this->fecha_inicio = $periodoContable->fecha_inicio;

        $this->fecha_fin = $periodoContable->fecha_fin;


        // Avisar al MediaManager qué categoría se está editando
        // $this->dispatch(
        //     'periodo-contable-media-cargar',
        //     periodoContableId: $periodoContable->id
        // );

        // Avisar que terminó la edición
        $this->dispatch(
            'periodo-contable-edicion-cargado',
            periodoContableId: $periodoContable->id
        );

        $this->resetValidation();
    }

    #[On('periodo-contable-edicion-cargado')]
    public function periodoContableEdicionCargada(): void
    {
        $this->dispatch('loading-stop');
    }

    /*
    |--------------------------------------------------------------------------
    | Cambio de local
    |--------------------------------------------------------------------------
    */

    public function localChanged(): void
    {
        // Al cambiar de local dejamos de editar el período anterior.
        $this->periodoContableId = null;

        // Reiniciar los datos dependientes del local.
        $this->reset([
            'nombre',
            'activo',
            'cerrado',
            'fecha_inicio',
            'fecha_fin',
        ]);

        $this->resetValidation();

        // Generar nuevamente nombre y fechas para el nuevo local.
        $this->bootFormulario();

        // Actualizar la tabla.
        $this->dispatch(
            'periodo-contable-filtros-actualizados',
            localId: $this->localId,
        );

        // Si posteriormente existe MediaManager.
        $this->dispatch('periodo-contable-media-reset');
    }

    /*
    |--------------------------------------------------------------------------
    | Guardar
    |--------------------------------------------------------------------------
    */

    public function save(): void
    {
        try {
            $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('loading-stop');

            throw $e;
        }

        /*
        |--------------------------------------------------------------------------
        | UPDATE
        |--------------------------------------------------------------------------
        */

        if ($this->periodoContableId) {

            $payload = $this->payload();

            //Logger()->info("ACTUALIZANDO PERIODO CONTABLE", ['payload'=>$payload]);

            $periodoContable = $this->command(
                'periodoContable.update',
                $payload,
            );

            $this->dispatch(
                'periodo-contable-actualizado',
            );

            $this->dispatch(
                'livewire:alert',
                [
                    'message' =>
                        'Periodo Contable Actualizado correctamente...',
                    'type' => 'success',
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | CREATE
        |--------------------------------------------------------------------------
        */

        else {

            $periodoContable = $this->command(
                'periodoContable.create',
                $this->payload()
            );

            $this->dispatch(
                'periodo-contable-creado'
            );

            $this->dispatch(
                'livewire:alert',
                [
                    'message' =>
                        'Periodo Contable Creado correctamente...',
                    'type' => 'success',
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Notificar página
        |--------------------------------------------------------------------------
        */

        $this->dispatch(
            'periodo-contable-guardado'
        );

        $this->dispatch('loading-stop');

        /*
        |--------------------------------------------------------------------------
        | Reset
        |--------------------------------------------------------------------------
        */

        $this->resetForm();
    }

    /*
    |--------------------------------------------------------------------------
    | Payload CQRS
    |--------------------------------------------------------------------------
    */

    private function payload(): array
    {
        return [
            'id' => $this->periodoContableId,

            'local_id' => $this->localId,

            'nombre' => $this->nombre,

            'activo' => $this->activo,

            'cerrado' => $this->cerrado,

            'fecha_inicio' => $this->fecha_inicio,

            'fecha_fin' => $this->fecha_fin,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Cancelar
    |--------------------------------------------------------------------------
    */

    public function cancel(): void
    {
        $this->resetForm();

        $this->dispatch(
            'livewire:alert',
            [
                'message' =>
                    'Acción cancelada...',
                'type' =>
                    'warning',
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Reset formulario
    |--------------------------------------------------------------------------
    */

    #[On('reset-form')]
    public function resetFormEvent(): void
    {
        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->reset([
            'periodoContableId',
            'activo',
            'cerrado',
        ]);

        $this->bootFormulario();

        $this->resetValidation();
    }

    public function render()
    {
        $locales = Local::orderBy('nombre')->get();

        if (!$this->localId) {
            $this->localId = $locales->first()?->id;
        }

        return view('modules.contabilidad.periodo-contable.form',
                ['locales' => $locales]
            );
    }
}
