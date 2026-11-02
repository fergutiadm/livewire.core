<?php

namespace App\Modules\Contabilidad\PeriodoContable\Livewire;

use App\Core\Context\LocalContext;
use App\Core\Context\PeriodoContext;
use Livewire\Component;
use Livewire\Attributes\On;

use App\Core\CQRS\HasCommands;
use App\Models\PeriodoContable;
use App\Modules\Configuracion\SetupConfig\Services\SetupConfigService;
use Illuminate\Support\Facades\Log;

class PeriodoContableModalEdit extends Component
{
    use HasCommands;

    public ?int $localId = null;

    public ?int $periodoContableId = null;

    public string $nombre = '';

    public string $fecha_inicio = '';

    public string $fecha_fin = '';

    public bool $activo = true;

    public bool $cerrado = false;

    public int $duracion_periodo = 6;

    protected $rules = [
        'nombre' => 'required|string|max:255',
        'localId' => 'required|integer|exists:locales,id',
        'duracion_periodo' => 'required|integer|min:1|max:30',
        'fecha_inicio' => 'required|date_format:Y-m-d',
        'fecha_fin' => 'required|date_format:Y-m-d|after_or_equal:fecha_inicio',
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

    public function mount(LocalContext $localContext, SetupConfigService $config): void {
        
        $this->localId = $localContext->id();

        $this->duracion_periodo = $config->get('duracion_periodo', 6);

        if ($this->localId && !$this->periodoContableId) {
            $this->bootFormulario();
        }

        // Log::info('PeriodoContableModalEdit | mount',[
        //     'nombre' => $this->nombre,
        //     'fecha_inicio' => $this->fecha_inicio,
        //     'fecha_fin' => $this->fecha_fin,
        // ]);
    }

    private function bootFormulario(): void
    {
        if (!$this->localId) {
            return;
        }

        $this->nombre = $this->generarNombreSugerido();

        $ultimoPeriodo = PeriodoContable::ultimoDeLocal(
            $this->localId
        );

        $inicio = $ultimoPeriodo
            ? $ultimoPeriodo->fecha_fin->copy()->addDay()
            : now()->startOfWeek();

        $this->fecha_inicio = $inicio->format('Y-m-d');

        $this->duracionChanged();

        Log::info('PeriodoContableModalEdit | bootFormulario',[
            'nombre' => $this->nombre,
            'fecha_inicio' => $this->fecha_inicio,
            'fecha_fin' => $this->fecha_fin,
        ]);
    }

    public function fechaInicioCambiada(): void
    {
        $this->dispatch('duracion-periodo-modificada');
        // Log::info('fechaInicioChanged');
    }

    public function fechaFinCambiada(): void
    {
        $this->duracion_periodo = \Carbon\Carbon::parse($this->fecha_fin)
                                            ->diffInDays(\Carbon\Carbon::parse($this->fecha_inicio))
                                            + 1;
        // Log::info('fechaFinChanged', [
        //     '$this->duracion_periodo' => $this->duracion_periodo
        // ]);
    }

    #[On('duracion-periodo-modificada')]
    public function duracionChanged(): void
    {
        if (
            !$this->fecha_inicio ||
            $this->duracion_periodo < 1 ||
            $this->duracion_periodo > 30
        ) {
            return;
        }

        try {
            $this->fecha_fin = \Carbon\Carbon::createFromFormat(
                '!Y-m-d',
                $this->fecha_inicio
            )
                ->addDays($this->duracion_periodo - 1)
                ->format('Y-m-d');
        } catch (\Throwable $e) {
            $this->addError(
                'fecha_inicio',
                'La fecha de inicio no es válida.'
            );
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

    #[On('edicion-periodo-contable-cargar-edicion')]
    public function edit(PeriodoContext $periodoContable): void
    {
        $this->periodoContableId = $periodoContable->id();

        $periodoContable = PeriodoContable::findOrFail($this->periodoContableId);

        $this->localId = $periodoContable->local_id;

        $this->nombre = $periodoContable->nombre;

        $this->activo = $periodoContable->activo ?? false;

        $this->cerrado = $periodoContable->cerrado ?? false;
        
        $this->fecha_inicio = $periodoContable->fecha_inicio
            ? \Carbon\Carbon::parse($periodoContable->fecha_inicio)->format('Y-m-d')
            : '';

        $this->fecha_fin = $periodoContable->fecha_fin
            ? \Carbon\Carbon::parse($periodoContable->fecha_fin)->format('Y-m-d')
            : '';

        // Avisar que terminó la edición
        // $this->dispatch(
        //     'edicion-periodo-contable-edicion-cargado',
        //     periodoContableId: $periodoContable->id
        // );

        // Log::info('edicion-periodo-contable-cargar-edicion | edit',[
        //     'nombre' => $this->nombre,
        //     'fecha_inicio' => $this->fecha_inicio,
        //     'fecha_fin' => $this->fecha_fin,
        // ]);

        $this->resetValidation();
    }

    // #[On('edicion-periodo-contable-edicion-cargado')]
    // public function periodoContableEdicionCargada(): void
    // {
    //     $this->dispatch('loading-stop');
    // }

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

            try {
                $periodoContable = $this->command(
                    'periodoContable.update',
                    $payload,
                );
            } catch(\LogicException $e) {
                $this->dispatch('loading-stop');
                $this->dispatch(
                    'livewire:alert',
                    [
                        'message' =>
                            $e->getMessage(),
                        'type' => 'error',
                    ]
                );

                return;
            }

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

            try {
                $periodoContable = $this->command(
                    'periodoContable.create',
                    $this->payload()
                );
            } catch(\LogicException $e) {
                $this->dispatch('loading-stop');
                $this->dispatch(
                    'livewire:alert',
                    [
                        'message' =>
                            $e->getMessage(),
                        'type' => 'error',
                    ]
                );

                return;
            }

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

        $this->dispatch('periodo-context-changed',
                periodoId: $periodoContable->id
        );

        $this->dispatch('loading-stop');

        $this->dispatch('close-modal-edit', 
                done: true
        );
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
        $this->dispatch('close-modal-edit');

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

    #[On('edicion-reset-form')]
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
        return view('modules.contabilidad.periodo-contable.modal-edit');
    }

}
