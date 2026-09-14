<?php

namespace App\Modules\Inventario\Local\Livewire;

use App\Core\CQRS\HasCommands;
use App\Models\Local;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

class LocalForm extends Component
{
    use HasCommands;

    public ?int $localId = null;

    public string $nombre = '';
    public string $descripcion = '';

    protected $rules = [
        'nombre' => 'required|string|max:255',

        'descripcion' => 'nullable|string|min:3|max:255',
    ];

    protected function messages(): array
    {
        return [
            'nombre.required' =>
                'El Nombre es obligatorio',

            'descripcion.min' =>
                'La descripción debe tener al menos 3 caracteres',

            'descripcion.max' =>
                'Descripción demasiado larga',
        ];
    }

    #[On('local-cargar-edicion')]
    public function edit(int $id): void
    {
        $local = Local::findOrFail($id);

        $this->localId = $local->id;

        $this->nombre = $local->nombre;

        $this->descripcion = $local->descripcion ?? '';

        // $this->orden_visual = (int) $local->orden_visual;

        // $this->icono = $local->icono;

        // $this->icono_secundario = $local->icono_secundario;

        // $this->color_bg = $local->color_bg;

        // $this->color_text = $local->color_text;

        // $this->color_badge = $local->color_badge;


        // Avisar al MediaManager qué categoría se está editando
        // $this->dispatch(
        //     'local-media-cargar',
        //     localId: $local->id
        // );

        // Avisar que terminó la edición
        $this->dispatch(
            'local-edicion-cargado',
            localId: $local->id
        );

        $this->resetValidation();
    }

    #[On('local-edicion-cargado')]
    public function localEdicionCargada(): void
    {
        $this->dispatch('loading-stop');
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

        if ($this->localId) {

            $payload = $this->payload();

            //Logger()->info("ACTUALIZANDO LOCAL", ['payload'=>$payload]);

            $local = $this->command(
                'local.update',
                $payload,
            );

            $this->dispatch(
                'local-actualizado',
            );

            $this->dispatch(
                'livewire:alert',
                [
                    'message' =>
                        'Local Actualizado correctamente...',
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

            $local = $this->command(
                'local.create',
                $this->payload()
            );

            $this->dispatch(
                'local-creado'
            );

            $this->dispatch(
                'livewire:alert',
                [
                    'message' =>
                        'Local Creado correctamente...',
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
            'local-guardado'
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
            'id' => $this->localId,

            'local_id' => $this->localId,

            'nombre' => $this->nombre,

            'descripcion' => $this->descripcion,

            // 'orden_visual' =>
            //     $this->orden_visual,

            // 'icono' =>
            //     $this->icono,

            // 'icono_secundario' =>
            //     $this->icono_secundario,

            // 'color_bg' =>
            //     $this->color_bg,

            // 'color_text' =>
            //     $this->color_text,

            // 'color_badge' =>
            //     $this->color_badge,
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
        // Log::info('Listener reset-form', [
        //     'localId' => $this->localId,
        // ]);

        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->reset([
            'localId',
            'nombre',
            'descripcion',
            // 'orden_visual',
            // 'icono',
            // 'icono_secundario',
            // 'color_bg',
            // 'color_text',
            // 'color_badge',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Valores iniciales
        |--------------------------------------------------------------------------
        */

        // $this->icono = 'bi-bag';

        // $this->icono_secundario = 'bi-basket';

        // $this->color_bg = 'bg-white';

        // $this->color_text = 'text-black';

        // $this->color_badge = null;

        // $this->orden_visual = 1;

        /*
        |--------------------------------------------------------------------------
        | Mantener local
        |--------------------------------------------------------------------------
        */


        $this->resetValidation();
    }

    public function render()
    {
        return view('modules.inventario.local.form');
    }
}
