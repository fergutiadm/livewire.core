<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;

use App\Models\Categoria;
use App\Models\Local;
use App\Http\Livewire\Traits\HandlesMediaUploads;
use App\Http\Livewire\Traits\HasReorderableItems;
use App\Http\Livewire\Traits\HasSortableMedia;
use App\Models\Media;
use Throwable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Categorias extends Component
{
    use WithPagination, WithFileUploads, HandlesMediaUploads, HasSortableMedia, HasReorderableItems;

    public $localId;
    public $locales;

    public $categoriaId;
    public $nombre;
    public $descripcion;
    public $porciento_descuento;

    public $categoriaIdToDelete = null;
    public bool $confirmingCategoriaDeletion = false;

    public bool $editAtributosShow = false;
    public $categoriaIdEditAtributos = null;
    public ?Categoria $categoriaEditAtributos = null;

    public $perPage = 10;
    public $formularioVisible = true;

    public bool $cardsDirty = false;
    public $orden_visual;

    public ?string $icono = 'bi-bag';
    public ?string $icono_secundario = 'bi-basket';
    public $color_bg = 'bg-white';  // Default color
    public $color_text = 'text-black'; // Default color
    public $color_badge = null;
    public $imagen_minimalista;
    public $imagen_representativa = null;

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
        'bi-cup-hot',         // café, bebidas calientes
        'bi-cup-straw',       // café, bebidas ligeras
        'bi-egg-fried',       // comidas
        'bi-basket',          // supermercado, compras
        'bi-bag',             // compras, tienda
        'bi-basket2',         // bebidas, licor

        // Tecnología y accesorios
        'bi-phone',           // móviles, accesorios
        'bi-laptop',          // laptops, tecnología
        'bi-headphones',      // música, accesorios
        'bi-controller',      // videojuegos, entretenimiento
        'bi-camera',          // fotografía, tecnología

        // Talleres y ferretería
        'bi-tools',           // taller, herramientas
        'bi-hammer',          // ferretería
        'bi-wrench',          // taller mecánico
        'bi-lightning',       // electricidad, energía
        'bi-fuel-pump',       // combustible, taller, servicios

        // Hogar y decoración
        'bi-house',           // hogar
        'bi-door-closed',     // inmobiliaria, hogar
        'bi-lightbulb',       // muebles, hogar
        'bi-brush',           // decoración, pintura
        'bi-paint-bucket',    // ferretería, pintura

        // Salud y cuidado personal
        'bi-heart-pulse',     // salud, bienestar
        'bi-droplet',         // líquidos, cuidado personal
        'bi-person',          // servicios, atención
        'bi-shield',          // seguridad, protección
        'bi-balloon',     // farmacia, primeros auxilios

        // Transporte y logística
        'bi-truck',           // transporte, logística
        'bi-bicycle',         // movilidad, delivery
        'bi-car-front',       // transporte
        'bi-geo-alt',         // ubicación, delivery
        'bi-globe',           // comercio global, online

        // Otros
        'bi-bricks',
        'bi-box2-heart',
        'bi-cake',
        'bi-camera-reels',
        'bi-claude',
    ];



    public $imagen_minimalista_upload;
    public array $minimalGallery = [
        'food.svg',
        'drink.svg',
        'tech.svg',
        'default.svg',
    ];

    protected $rules = [
        'nombre'                    => 'required|string|max:255',
        'localId'                   => 'required|exists:locales,id',
        'porciento_descuento'       => 'numeric|min:0',
        'descripcion'               => 'required|string|max:255',
        'imagenes.*'                => 'image|max:1024',
        'icono'                     => 'nullable|string|max:30',
        'icono_secundario'          => 'nullable|string|max:30',
        'color_bg'                  => 'nullable|string',
        'color_text'                => 'nullable|string',
        'imagen_minimalista'        => 'nullable|string|max:50',
        'imagen_representativa'     => 'nullable|image|max:5120', // máximo 5MB
        'imagen_minimalista_upload' => 'nullable|image|max:1024',
    ];



    protected function messages()
    {
        return [
            'nombre.required'             => 'El Nombre es obligatorio',
            'localId.required'            => 'El Local es obligatorio',
            'porciento_descuento.numeric' => 'Tiene que ser un número mayor que Cero',
            'porciento_descuento.min'     => 'Tiene que ser un número mayor que Cero',
            'descripcion.required'        => 'La descripción es obligatoria',
            'descripcion.max'             => 'Descripción demasiado larga',
            'imagenes.image'              => 'Solo se admiten imágenes',
            'imagenes.max'                => 'Imagen muy grande mayor de 1 MB',

            'icono.string'                => 'El icono debe ser texto válido',
            'icono.max'                   => 'El icono no puede exceder 30 caracteres',

            'icono_secundario.string'     => 'El icono secundario debe ser texto válido',
            'icono_secundario.max'        => 'El icono secundario no puede exceder 30 caracteres',

            'color_bg.string'             => 'El color de fondo debe ser un valor válido',
            // 'color_bg.max'                => 'El color de fondo no puede exceder 7 caracteres (formato HEX)',

            'color_text.string'           => 'El color de texto debe ser un valor válido',
            // 'color_text.max'              => 'El color de texto no puede exceder 7 caracteres (formato HEX)',

            'imagen_minimalista.string'   => 'La imagen minimalista debe ser texto válido',
            'imagen_minimalista.max'      => 'La imagen minimalista no puede exceder 50 caracteres',
        ];
    }

    public function selectPalette(string $bg, string $text): void
    {
        $this->color_bg = $bg;
        $this->color_text = $text;
    }

    private function paletteIsValid(): bool
    {
        return collect($this->palette)->contains(fn ($p) =>
            $p['bg'] === $this->color_bg && $p['text'] === $this->color_text
        );
    }

    protected function persistMediaOrder(): void
    {
        foreach ($this->medias as $media) {
            Media::where('id', $media['id'])->update([
                'orden' => $media['orden'],
                'is_primary' => $media['is_primary'] ?? false
            ]);
        }
    }

    /* ================================
        Drag & Drop Medias
    =============================== */
    public function reordenarMedias($orderedIds)
    {
        $this->reorderMediaPersisted($orderedIds);
        $this->dispatch('mediasReordenadas');
    }

    /* ================================
    Marcar primaria
    =============================== */
    public function marcarPrimariaCategoria($mediaId)
    {
        $this->markPrimaryInMemory($mediaId);
        $this->setPrimaryPersisted($mediaId);
    }

    public function mount($localId = null)
    {
        $this->loadLocales($localId);
    }

    public function loadLocales($localId = null)
    {
        $this->locales = Local::orderBy('nombre')->get();
        $this->localId = $localId && $this->locales->contains('id', $localId)
            ? $localId
            : ($this->locales->first()?->id ?? null);
    }

    /* ================================
       CRUD Categorías
    ================================ */

    public function save()
    {
        $this->validate($this->rules);

        if (!$this->paletteIsValid()) {
            $this->addError('color_bg', 'La combinación de colores no es válida.');
            return;
        }

        if ($this->imagen_minimalista_upload) {
            $path = $this->imagen_minimalista_upload->store('minimal','public');
            $this->imagen_minimalista = $path;
        }


        $data = [
            'nombre'              => $this->nombre,
            'descripcion'         => $this->descripcion,
            'porciento_descuento' => $this->porciento_descuento,
            'local_id'            => $this->localId,
            'orden_visual'        => $this->categoriaId
                ? Categoria::find($this->categoriaId)->orden_visual
                : (Categoria::where('local_id', $this->localId)->max('orden_visual') ?? 0) + 1,
            'icono'               => $this->icono,
            'icono_secundario'    => $this->icono_secundario,
            'color_bg'            => $this->color_bg,
            'color_text'          => $this->color_text,
        ];

        //Log::error('data', $data);

        try {
            if ($this->categoriaId) {
                $categoria = Categoria::findOrFail($this->categoriaId);
            } else {
                $categoria = new Categoria();
                $this->categoriaId = $categoria->id;
            }

            $categoria->nombre = $data['nombre'];
            $categoria->descripcion = $data['descripcion'];
            $categoria->porciento_descuento = $data['porciento_descuento'];
            $categoria->local_id = $data['local_id'];
            $categoria->orden_visual = $data['orden_visual'];
            $categoria->icono = $data['icono'];
            $categoria->icono_secundario = $data['icono_secundario'];
            $categoria->color_bg = $data['color_bg'];
            $categoria->color_text = $data['color_text'];
            $categoria->save();

            // Subir imágenes usando el trait
            $this->subirImagenes($categoria);

        } catch (Throwable $e) {
            $this->dispatch('livewire:alert', [
                'message' => $this->categoriaId ? 'Error Actualizando Categoría' : 'Error Creando Categoría',
                'type' => 'error',
            ]);
            $this->logError('save', $e, ['categoria_id' => $this->categoriaId]);
            return;
        }

        $this->dispatch('livewire:alert', [
            'message' => $this->categoriaId ? 'Categoría actualizada correctamente' : 'Categoría creada correctamente',
            'type' => 'success',
        ]);

        $this->resetForm();
        $this->dispatch('categoriasActualizadas');
    }

    #[On('edit')]
    public function edit($id)
    {
        try {
            $categoria = Categoria::findOrFail($id);
        } catch (Throwable $e) {
            $this->logError('edit', $e, ['categoria_id' => $id]);
            return;
        }

        $this->categoriaId            = $categoria->id;
        $this->nombre                 = $categoria->nombre;
        $this->descripcion            = $categoria->descripcion;
        $this->porciento_descuento    = $categoria->porciento_descuento;
        $this->icono                  = $categoria->icono;
        $this->icono_secundario       = $categoria->icono_secundario;
        $this->color_bg               = $categoria->color_bg;
        $this->color_text             = $categoria->color_text;
        $this->imagen_minimalista     = $categoria->imagen_minimalista;
        $this->imagen_representativa  = $categoria->imagen_representativa;

        $this->loadMedias($categoria);
    }

    #[On('editAtributos')]
    public function editAtributos($id=0) {
        $this->categoriaEditAtributos = Categoria::find($id);
        if(!$this->categoriaEditAtributos){
            $this->dispatch('livewire:alert', [
                'message' => 'Categoria no encontrada. editAtributos',
                'type' => 'error',
            ]); return;
        }

        $this->editAtributosShow = true;
        $this->categoriaIdEditAtributos = $id;
    }

    public function intentarCerrarModal() {
        if ($this->tieneCambiosPendientes()) {
            // Logger('livewire:confirm', ['$this->getId()'=>$this->getId()]);
        $this->dispatch('livewire:confirm', [
            'message' => 'No ha salvado aún. Si abandona no habrá cambios.',
            'onConfirm' => 'cancelEditAtributos',
            'componentId' => $this->getId(), ]);
            return;
        }

        $this->cancelEditAtributos(); }

    #[On('cerrar-modal-edit-atributos')]
    public function cerrarModalEditAtributos() {
        $this->cancelEditAtributos();
    }

    public function cancelEditAtributos()
    {
        $this->reset('categoriaIdEditAtributos', 'categoriaEditAtributos', 'editAtributosShow');
        $this->resetValidation();
    }

    public function cancelDelete() {
        $this->categoriaIdToDelete = null;
    }

    #[On('confirmDelete')]
    public function confirmDelete($id)
    {
        $this->categoriaIdToDelete = $id;
        $this->confirmingCategoriaDeletion = true;
    }

    public function delete()
    {
        if (!$this->categoriaIdToDelete) return;

        try {
            Categoria::findOrFail($this->categoriaIdToDelete)->delete();
        } catch (Throwable $e) {
            $this->logError('delete', $e, ['categoria_id' => $this->categoriaIdToDelete]);
            return;
        }

        $this->confirmingCategoriaDeletion = false;
        $this->categoriaIdToDelete = null;

        $this->resetForm();
        $this->dispatch('categoriasActualizadas');
    }

    public function cancel()
    {
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset([
            'categoriaId', 'nombre', 'descripcion', 'porciento_descuento', 'orden_visual', 'icono', 'color_bg', 'color_text'
        ]);
        $this->resetMediaProperties();
        $this->resetValidation();
    }

    /* ================================
       Misc
    ================================ */

    #[On('cards-dirty-updated')]
    public function actualizarCardsDirty(bool $dirty)
    {
        $this->cardsDirty = $dirty;
    }

    public function tieneCambiosPendientes(): bool {
        return $this->cardsDirty;
    }

    public function render()
    {
        return view('livewire.admin.categorias')
            ->layout('layouts.app_admin');
    }
}
