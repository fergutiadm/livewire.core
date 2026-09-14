<?php

namespace App\Modules\Inventario\Categoria\Livewire;

use App\Core\CQRS\HasCommands;
use App\Models\Categoria;
use App\Models\Local;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class CategoriaForm extends Component
{
    use HasCommands;
    use WithFileUploads;

    public $imagenes = [];

    public ?int $categoriaId = null;
    public ?int $localId = null;

    public string $nombre = '';
    public string $descripcion = '';

    public bool $mostrarConfiguracionVisual = false;

    public int $orden_visual = 1;
    public float $porciento_descuento = 0;

    public ?string $icono = 'bi-bag';
    public ?string $icono_secundario = 'bi-basket';

    public ?string $color_bg = 'bg-white';
    public ?string $color_text = 'text-black';
    public ?string $color_badge = null;

    /*
    |--------------------------------------------------------------------------
    | Imágenes
    |--------------------------------------------------------------------------
    |
    | En CREATE:
    |   TemporaryUploadedFile
    |
    | En EDIT:
    |   string con la ruta existente
    |
    */

    public $imagen_minimalista = null;
    public $imagen_representativa = null;

    public $locales;

    public array $palette = [
        ['bg' => 'bg-white',      'text' => 'text-black'],
        ['bg' => 'bg-blue-500',   'text' => 'text-white'],
        ['bg' => 'bg-green-500',  'text' => 'text-white'],
        ['bg' => 'bg-yellow-500', 'text' => 'text-black'],
        ['bg' => 'bg-purple-500', 'text' => 'text-white'],
        ['bg' => 'bg-pink-500',   'text' => 'text-white'],
        ['bg' => 'bg-indigo-500', 'text' => 'text-white'],
        ['bg' => 'bg-teal-500',   'text' => 'text-white'],
        ['bg' => 'bg-orange-500', 'text' => 'text-black'],
        ['bg' => 'bg-cyan-500',   'text' => 'text-black'],
        ['bg' => 'bg-black',      'text' => 'text-white'],
    ];

    public array $iconGallery = [
        // Comidas y bebidas
        'bi-cup-hot',
        'bi-cup-straw',
        'bi-egg-fried',
        'bi-basket',
        'bi-bag',
        'bi-basket2',

        // Tecnología y accesorios
        'bi-phone',
        'bi-laptop',
        'bi-headphones',
        'bi-controller',
        'bi-camera',

        // Talleres y ferretería
        'bi-tools',
        'bi-hammer',
        'bi-wrench',
        'bi-lightning',
        'bi-fuel-pump',

        // Hogar y decoración
        'bi-house',
        'bi-door-closed',
        'bi-lightbulb',
        'bi-brush',
        'bi-paint-bucket',

        // Salud y cuidado personal
        'bi-heart-pulse',
        'bi-droplet',
        'bi-person',
        'bi-shield',
        'bi-balloon',

        // Transporte y logística
        'bi-truck',
        'bi-bicycle',
        'bi-car-front',
        'bi-geo-alt',
        'bi-globe',

        // Otros
        'bi-bricks',
        'bi-box2-heart',
        'bi-cake',
        'bi-camera-reels',
        'bi-claude',
    ];

    protected $rules = [
        'nombre' => 'required|string|max:255',

        'localId' => 'required|exists:locales,id',

        'porciento_descuento' => 'numeric|min:0',

        'descripcion' => 'nullable|string|min:3|max:255',

        'icono' => 'nullable|string|max:30',
        'icono_secundario' => 'nullable|string|max:30',

        'color_bg' => 'nullable|string',
        'color_text' => 'nullable|string',
        'color_badge' => 'nullable|string',

        /*
        |--------------------------------------------------------------------------
        | Imagen minimalista
        |--------------------------------------------------------------------------
        |
        | Puede ser:
        | - string durante edición
        | - TemporaryUploadedFile cuando se selecciona una nueva
        |
        */
        'imagen_minimalista' => 'nullable',

        'imagen_representativa' => 'nullable|image|max:5120',
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

            'porciento_descuento.numeric' =>
                'Tiene que ser un número mayor que Cero',

            'porciento_descuento.min' =>
                'Tiene que ser un número mayor que Cero',

            'descripcion.min' =>
                'La descripción debe tener al menos 3 caracteres',

            'descripcion.max' =>
                'Descripción demasiado larga',

            'icono.string' =>
                'El icono debe ser texto válido',

            'icono.max' =>
                'El icono no puede exceder 30 caracteres',

            'icono_secundario.string' =>
                'El icono secundario debe ser texto válido',

            'icono_secundario.max' =>
                'El icono secundario no puede exceder 30 caracteres',

            'color_bg.string' =>
                'El color de fondo debe ser un valor válido',

            'color_text.string' =>
                'El color de texto debe ser un valor válido',

            'color_badge.string' =>
                'El color del badge debe ser un valor válido',

            'imagen_representativa.image' =>
                'Solo se admiten imágenes',

            'imagen_representativa.max' =>
                'La imagen representativa no puede superar 5 MB',
        ];
    }

    public function diagnosticarImagenTemporal()
    {
        $imagen = $this->imagenes[0] ?? null;

        if (!$imagen) {
            dd('No hay imagen temporal');
        }

        $path = $imagen->getRealPath();

        dd([
            'class' => get_class($imagen),
            'path' => $path,
            'exists' => file_exists($path),
            'size' => $imagen->getSize(),
            'mime' => $imagen->getMimeType(),
            'client_name' => $imagen->getClientOriginalName(),
            'client_extension' => $imagen->getClientOriginalExtension(),
            'first_bytes_hex' => bin2hex(file_get_contents($path, false, null, 0, 16)),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Inicialización
    |--------------------------------------------------------------------------
    */

    public function mount(): void
    {
        $this->loadData();

        $this->dispatch(
            'categoria-filtros-actualizados',
            localId: $this->localId,
        );
    }

    public function loadData(): void
    {
        $this->locales = Local::orderBy('nombre')->get();

        if (!$this->localId) {
            $this->localId = $this->locales->first()?->id;
        }

    }

    #[On('categoria-edicion-cargada')]
    public function categoriaEdicionCargada(): void
    {
        $this->dispatch('loading-stop');
    }

    /*
    |--------------------------------------------------------------------------
    | Edición
    |--------------------------------------------------------------------------
    */

    #[On('categoria-attributes-cargar-edicion')]
    public function editAttributes(int $id): void
    {
        // Avisar al AttributesManager qué categoría se está editando
        $this->dispatch(
            'categoria-attributes-cargar',
            categoriaId: $id
        );
    }

    #[On('categoria-cargar-edicion')]
    public function edit(int $id): void
    {
        $categoria = Categoria::findOrFail($id);

        $this->categoriaId = $categoria->id;

        $this->localId = $categoria->local_id;

        $this->nombre = $categoria->nombre;

        $this->descripcion = $categoria->descripcion ?? '';

        $this->orden_visual = (int) $categoria->orden_visual;

        $this->porciento_descuento = (float) $categoria->porciento_descuento;

        $this->icono = $categoria->icono;

        $this->icono_secundario = $categoria->icono_secundario;

        $this->color_bg = $categoria->color_bg;

        $this->color_text = $categoria->color_text;

        $this->color_badge = $categoria->color_badge;

        /*
        |--------------------------------------------------------------------------
        | IMPORTANTE
        |--------------------------------------------------------------------------
        |
        | Aquí estamos cargando la ruta existente como STRING.
        | Blade debe detectar que no es un TemporaryUploadedFile
        | antes de llamar temporaryUrl().
        |
        */

        $this->imagen_minimalista =
            $categoria->imagen_minimalista;

        /*
        |--------------------------------------------------------------------------
        | La imagen representativa la gestiona MediaManager
        |--------------------------------------------------------------------------
        */

        $this->imagen_representativa = null;

        // Avisar al MediaManager qué categoría se está editando
        $this->dispatch(
            'categoria-media-cargar',
            categoriaId: $categoria->id
        );

        // Avisar que terminó la edición
        $this->dispatch(
            'categoria-edicion-cargada',
            categoriaId: $categoria->id
        );

        $this->resetValidation();
    }

    /*
    |--------------------------------------------------------------------------
    | Iconos
    |--------------------------------------------------------------------------
    */

    public function seleccionarIcono(string $icon): void
    {
        $this->icono = $icon;
    }

    public function seleccionarIconoSecundario(string $icon): void
    {
        $this->icono_secundario = $icon;
    }

    /*
    |--------------------------------------------------------------------------
    | Paleta
    |--------------------------------------------------------------------------
    */

    public function selectPalette(
        string $bg,
        string $text
    ): void {
        $this->color_bg = $bg;
        $this->color_text = $text;
    }

    private function paletteIsValid(): bool
    {
        return collect($this->palette)->contains(
            fn ($p) =>
                $p['bg'] === $this->color_bg &&
                $p['text'] === $this->color_text
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Cambio de local
    |--------------------------------------------------------------------------
    */

    public function localChanged(): void
    {
        $this->dispatch(
            'categoria-media-reset'
        );

        $this->dispatch(
            'categoria-filtros-actualizados',
            localId: $this->localId,
        );
    }

    public function categoriaChanged(): void
    {
        $this->dispatch(
            'categoria-filtros-actualizados',
            localId: $this->localId,
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

            //Logger()->info("ACTUALIZANDO PRODUCTO", ['payload'=>$payload]);

            $categoria = $this->command(
                'categoria.update',
                $payload,
            );

            $this->dispatch(
                'categoria-actualizada',
                categoriaId: $categoria->id,
            )->to(CategoriaMediaManager::class);

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

            $categoria = $this->command(
                'categoria.create',
                $this->payload()
            );

            $this->dispatch(
                'categoria-creada',
                categoriaId: $categoria->id,
            )->to(CategoriaMediaManager::class);

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
            'categoria-guardada'
        );

        /*
        |--------------------------------------------------------------------------
        | Reset
        |--------------------------------------------------------------------------
        */

        //$this->resetForm();
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

            'local_id' => $this->localId,

            'nombre' => $this->nombre,

            'descripcion' => $this->descripcion,

            'porciento_descuento' =>
                $this->porciento_descuento,

            'orden_visual' =>
                $this->orden_visual,

            /*
            |--------------------------------------------------------------------------
            | IMPORTANTE
            |--------------------------------------------------------------------------
            |
            | El DTO actualmente espera ?string.
            | Por tanto NO enviamos TemporaryUploadedFile.
            |
            */

            'imagen_minimalista' =>
                is_string($this->imagen_minimalista)
                    ? $this->imagen_minimalista
                    : null,

            'icono' =>
                $this->icono,

            'icono_secundario' =>
                $this->icono_secundario,

            'color_bg' =>
                $this->color_bg,

            'color_text' =>
                $this->color_text,

            'color_badge' =>
                $this->color_badge,
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
            'categoria-media-reset'
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
        Log::info('Listener reset-form', [
            'categoriaId' => $this->categoriaId,
        ]);

        $this->resetForm();
    }

    #[On('media-procesada')]
    public function categoriaMediaProcesada(): void
    {
        Log::info('CategoriaForm::categoriaMediaProcesada');

        $this->dispatch('categoria-media-reset')
        ->to(CategoriaMediaManager::class);

        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->reset([
            'categoriaId',
            'nombre',
            'descripcion',
            'porciento_descuento',
            'orden_visual',
            'icono',
            'icono_secundario',
            'color_bg',
            'color_text',
            'color_badge',
            'imagen_minimalista',
            'imagen_representativa',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Valores iniciales
        |--------------------------------------------------------------------------
        */

        $this->icono = 'bi-bag';

        $this->icono_secundario = 'bi-basket';

        $this->color_bg = 'bg-white';

        $this->color_text = 'text-black';

        $this->color_badge = null;

        $this->orden_visual = 1;

        $this->porciento_descuento = 0;

        /*
        |--------------------------------------------------------------------------
        | Mantener local
        |--------------------------------------------------------------------------
        */

        if (!$this->localId) {
            $this->localId =
                $this->locales->first()?->id;
        }

        $this->resetValidation();
    }

    /*
    |--------------------------------------------------------------------------
    | Render
    |--------------------------------------------------------------------------
    */

    public function render()
    {
        return view(
            //'modules.inventario.categoria.form-test'
            'modules.inventario.categoria.form'
        );
    }
}
