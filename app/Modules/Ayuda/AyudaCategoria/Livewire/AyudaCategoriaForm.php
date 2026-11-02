<?php

namespace App\Modules\Ayuda\AyudaCategoria\Livewire;

use App\Core\CQRS\HasCommands;
use App\Models\AyudaCategoria;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

class AyudaCategoriaForm extends Component
{
    use HasCommands;

    public ?int $categoriaId = null;

    public string $nombre = '';
    public string $slug = '';
    public string $descripcion = '';
    public int $orden_visual = 0;
    public bool $activa = true;

    protected $rules = [
        'nombre' => 'required|string|max:150',

        // 'slug' => 'required|string|max:160',

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

    /*
    |--------------------------------------------------------------------------
    | Inicialización
    |--------------------------------------------------------------------------
    */

    public function mount(): void
    {
        $this->dispatch(
            'ayuda-categoria-filtros-actualizados'
        );
    }

    #[On('ayuda-categoria-edicion-cargada')]
    public function categoriaEdicionCargada(): void
    {
        $this->dispatch('loading-stop');
    }

    /*
    |--------------------------------------------------------------------------
    | Edición
    |--------------------------------------------------------------------------
    */

    #[On('ayuda-categoria-attributes-cargar-edicion')]
    public function editAttributes(int $id): void
    {
        // Avisar al AttributesManager qué categoría se está editando
        $this->dispatch(
            'ayuda-categoria-attributes-cargar',
            categoriaId: $id
        );
    }

    #[On('ayuda-categoria-cargar-edicion')]
    public function edit(int $id): void
    {
        $categoria = AyudaCategoria::findOrFail($id);

        $this->categoriaId = $categoria->id;

        $this->nombre = $categoria->nombre;

        $this->slug = $categoria->slug;

        $this->descripcion = $categoria->descripcion ?? '';

        $this->orden_visual = (int) $categoria->orden_visual;

        $this->activa = (bool) $categoria->activa;

        // Avisar al MediaManager qué categoría se está editando
        $this->dispatch(
            'ayuda-categoria-media-cargar',
            categoriaId: $categoria->id
        );

        // Avisar que terminó la edición
        $this->dispatch(
            'ayuda-categoria-edicion-cargada',
            categoriaId: $categoria->id
        );

        $this->resetValidation();
    }

    public function categoriaChanged(): void
    {
        $this->dispatch(
            'ayuda-categoria-filtros-actualizados'
        );
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

        if ($this->categoriaId) {

            $payload = $this->payload();

            $categoria = $this->command(
                'ayudaCategoria.update',
                $payload,
            );

            $this->dispatch(
                'livewire:alert',
                [
                    'message' =>
                        'Categoría Actualizada correctamente...',
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
            $payload = $this->payload();
            
            $categoria = $this->command(
                'ayudaCategoria.create',
                $payload
            );

            $this->dispatch(
                'livewire:alert',
                [
                    'message' =>
                        'Categoría Creada correctamente...',
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
            'ayuda-categoria-guardada'
        );

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
            'id' => $this->categoriaId,

            'nombre' => $this->nombre,

            'slug' => ($this->slug && $this->slug != '') ? $this->slug : generate_slug($this->nombre),

            'descripcion' => $this->descripcion,

            'orden_visual' =>
                $this->orden_visual,

            'activa' =>
                $this->activa,
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
            'categoriaId',
            'nombre',
            'slug',
            'descripcion',
            'orden_visual',
            'activa',
        ]);

        $this->resetValidation();
    }

    /*
    |--------------------------------------------------------------------------
    | Render
    |--------------------------------------------------------------------------
    */


    public function render()
    {
        return view('modules.ayuda.ayuda-categoria.form');
    }
}