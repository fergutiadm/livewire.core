<?php

namespace App\Modules\Ayuda\AyudaArticulo\Livewire;

use App\Core\CQRS\HasCommands;
use App\Models\AyudaArticulo;
use App\Models\AyudaCategoria;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\On;
use Livewire\Component;

use Illuminate\Support\Str;

class AyudaArticuloForm extends Component
{
    use HasCommands;

    /*
    |--------------------------------------------------------------------------
    | Identificadores
    |--------------------------------------------------------------------------
    */

    public ?int $ayudaArticuloId = null;

    public ?int $categoriaId = null;

    /*
    |--------------------------------------------------------------------------
    | Datos del artículo
    |--------------------------------------------------------------------------
    */

    public string $titulo = '';

    public string $slug = '';

    public string $resumen = '';

    // HTML generado por Tiptap.
    public string $contenido = '';

    public string $contexto = '';

    public ?int $orden_visual = null;

    // Solo informativo en el formulario.
    // La publicación se gestiona mediante su propio comando CQRS.
    public bool $publicado = false;

    /*
    |--------------------------------------------------------------------------
    | Validación
    |--------------------------------------------------------------------------
    */

    protected function rules(): array
    {
        return [
            'categoriaId' => [
                'required',
                'integer',
                'exists:ayuda_categorias,id',
            ],

            'titulo' => [
                'required',
                'string',
                'max:200',
            ],

            'slug' => [
                'required',
                'string',
                'max:220',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
            ],

            'resumen' => [
                'nullable',
                'string',
            ],

            'contenido' => [
                'present',
                'string',
            ],

            'contexto' => [
                'nullable',
                'string',
                'max:160',
            ],

            // 'orden_visual' => [
            //     'nullable',
            //     'integer',
            //     'min:0',
            // ],
        ];
    }

    protected function messages(): array
    {
        return [
            'categoriaId.required' =>
                'Seleccione una categoría.',

            'categoriaId.exists' =>
                'La categoría seleccionada no existe.',

            'titulo.required' =>
                'El título es obligatorio.',

            'titulo.max' =>
                'El título no puede superar los 200 caracteres.',

            'slug.required' =>
                'No se pudo generar el identificador del artículo.',

            'slug.regex' =>
                'El identificador contiene caracteres no permitidos.',

            'slug.max' =>
                'El identificador no puede superar los 220 caracteres.',

            'contexto.max' =>
                'El contexto no puede superar los 160 caracteres.',

            // 'orden_visual.min' =>
            //     'El orden visual no puede ser negativo.',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Cambios del formulario
    |--------------------------------------------------------------------------
    */

    public function updatedTitulo(string $value): void
    {
        // El slug de un artículo existente permanece estable.
        if ($this->ayudaArticuloId !== null) {
            return;
        }

        $this->slug = Str::slug($value);
    }

    public function categoriaChanged(): void
    {
        // Cambiar de categoría no debe borrar el formulario.
        $this->resetValidation('categoriaId');
    }

    /*
    |--------------------------------------------------------------------------
    | Guardar
    |--------------------------------------------------------------------------
    */

    public function save(): void
    {
        try {
            $this->normalizarFormulario();

            $this->validate();

            $payload = $this->payload();

            if ($this->ayudaArticuloId !== null) {

                $this->command(
                    'ayudaArticulo.update',
                    $payload
                );

                $mensaje = 'Artículo actualizado correctamente.';

            } else {

                $this->command(
                    'ayudaArticulo.create',
                    $payload
                );

                $mensaje = 'Artículo creado como borrador.';
            }

            $this->resetForm();

            $this->dispatch('ayuda-articulo-guardado');

            $this->dispatch('livewire:alert', [
                'message' => $mensaje,
                'type' => 'success',
            ]);

        } catch (ValidationException $e) {

            throw $e;

        } finally {

            $this->dispatch('loading-stop');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Normalización
    |--------------------------------------------------------------------------
    */

    private function normalizarFormulario(): void
    {
        $this->titulo = trim($this->titulo);

        $this->resumen = trim($this->resumen);

        $this->contexto = trim($this->contexto);

        // No aplicamos trim() ni strip_tags() al HTML de Tiptap.
        // La sanitización se realizará en la capa de dominio.
        if ($this->ayudaArticuloId === null) {

            $this->slug = Str::slug($this->titulo);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Edición
    |--------------------------------------------------------------------------
    */

    #[On('ayuda-articulo-cargar-edicion')]
    public function edit(int $id): void
    {
        $articulo = AyudaArticulo::findOrFail($id);

        $this->ayudaArticuloId = $articulo->id;

        $this->categoriaId = $articulo->categoria_id;

        $this->titulo = $articulo->titulo;

        $this->slug = $articulo->slug;

        $this->resumen = $articulo->resumen ?? '';

        $this->contenido = $articulo->contenido ?? '';

        $this->contexto = $articulo->contexto ?? '';

        $this->orden_visual = $articulo->orden_visual ?? 0;

        $this->publicado = (bool) $articulo->publicado;

        $this->resetValidation();

        $this->dispatch('ayuda-articulo-edicion-cargado');

        $this->dispatch('loading-stop');
    }

    /*
    |--------------------------------------------------------------------------
    | Payload CQRS
    |--------------------------------------------------------------------------
    */

    private function payload(): array
    {
        $data = [
            'categoria_id' => $this->categoriaId,

            'titulo' => $this->titulo,

            'slug' => $this->slug,

            'resumen' => $this->resumen !== ''
                ? $this->resumen
                : null,

            'contenido' => $this->contenido,

            'contexto' => $this->contexto !== ''
                ? $this->contexto
                : null,

            'orden_visual' => $this->orden_visual,
        ];

        if ($this->ayudaArticuloId !== null) {

            $data['id'] = $this->ayudaArticuloId;

        } else {

            // Todo artículo nuevo comienza como borrador.
            $data['publicado'] = false;
        }

        return $data;
    }

    /*
    |--------------------------------------------------------------------------
    | Cancelar
    |--------------------------------------------------------------------------
    */

    public function cancel(): void
    {
        $this->resetForm();

        $this->dispatch('loading-stop');
    }

    /*
    |--------------------------------------------------------------------------
    | Reiniciar formulario desde otros componentes
    |--------------------------------------------------------------------------
    */

    #[On('reset-form')]
    public function resetFormEvent(): void
    {
        $this->resetForm();
    }

    /*
    |--------------------------------------------------------------------------
    | Reiniciar formulario
    |--------------------------------------------------------------------------
    */

    private function resetForm(): void
    {
        $this->reset([
            'ayudaArticuloId',
            'categoriaId',
            'titulo',
            'slug',
            'resumen',
            'contenido',
            'contexto',
            'orden_visual',
            'publicado',
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
        return view('modules.ayuda.ayuda-articulo.form', [
            'categorias' => AyudaCategoria::query()
                ->where(function ($query) {

                    $query->where('activa', true);

                    // Durante la edición, conservar la categoría
                    // seleccionada aunque haya sido desactivada.
                    if ($this->categoriaId !== null) {
                        $query->orWhere(
                            'id',
                            $this->categoriaId
                        );
                    }
                })
                ->orderBy('orden_visual')
                ->orderBy('nombre')
                ->get(),
        ]);
    }
}
