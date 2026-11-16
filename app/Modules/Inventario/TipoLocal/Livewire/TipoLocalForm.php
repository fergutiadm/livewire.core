<?php

namespace App\Modules\Inventario\TipoLocal\Livewire;

use App\Core\CQRS\HasCommands;
use App\Models\TipoLocal;
use Livewire\Attributes\On;
use Livewire\Component;

class TipoLocalForm extends Component
{
    use HasCommands;

    public ?int $tipoLocalId = null;

    public string $codigo = '';
    public string $nombre = '';
    public string $descripcion = '';
    public bool $activo = false;

    protected $rules = [
        'codigo' => 'required|string|max:50',

        'nombre' => 'required|string|max:100',

        'descripcion' => 'nullable|string|min:3',
    ];

    protected function messages(): array
    {
        return [
            'codigo.required' =>
                'El Código es obligatorio',

            'nombre.required' =>
                'El Nombre es obligatorio',

            'descripcion.min' =>
                'La descripción debe tener al menos 3 caracteres',
        ];
    }

    #[On('tipo-local-cargar-edicion')]
    public function edit(int $id): void
    {
        $tipoLocal = TipoLocal::findOrFail($id);

        $this->tipoLocalId = $tipoLocal->tipo_local_id;

        $this->codigo = $tipoLocal->codigo;

        $this->nombre = $tipoLocal->nombre;

        $this->descripcion = $tipoLocal->descripcion ?? '';

        $this->activo = (bool) $tipoLocal->activo;

        // $this->icono = $tipoLocal->icono;

        // $this->icono_secundario = $tipoLocal->icono_secundario;

        // $this->color_bg = $tipoLocal->color_bg;

        // $this->color_text = $tipoLocal->color_text;

        // $this->color_badge = $tipoLocal->color_badge;


        // Avisar al MediaManager qué categoría se está editando
        // $this->dispatch(
        //     'tipo-local-media-cargar',
        //     localId: $tipoLocal->id
        // );

        // Avisar que terminó la edición
        $this->dispatch(
            'tipo-local-edicion-cargado',
            localId: $tipoLocal->id
        );

        $this->resetValidation();
    }

    #[On('tipo-local-edicion-cargado')]
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

            $tipoLocal = $this->command(
                'tipoLocal.update',
                $payload,
            );

            $this->dispatch(
                'tipo-local-actualizado',
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

            $tipoLocal = $this->command(
                'tipoLocal.create',
                $this->payload()
            );

            $this->dispatch(
                'tipo-local-creado'
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
            'tipo-local-guardado'
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

            'tipo_local_id' => $this->tipoLocalId,

            'codigo' => $this->codigo,

            'nombre' => $this->nombre,

            'descripcion' => $this->descripcion,

            'activo' => $this->activo,

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
            'tipoLocalId',
            'codigo',
            'nombre',
            'descripcion',
            'activo',
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
        return view('modules.inventario.tipo-local.form');
    }
}