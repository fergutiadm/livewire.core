<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;

use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Local;
use App\Models\Media;   
use App\Models\Moneda;

use App\Http\Livewire\Traits\HandlesMediaUploads;
use App\Http\Livewire\Traits\HasReorderableItems;
use App\Http\Livewire\Traits\HasSortableMedia;
use Throwable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Picqer\Barcode\BarcodeGeneratorPNG;

class Productos extends Component
{
    use WithPagination, WithFileUploads, HandlesMediaUploads, HasSortableMedia, HasReorderableItems;

    #[Url(except: '')]
    public $localId;

    public $locales;
    public $categorias;
    public $monedas;

    public $productoId;
    public $productoIdToDelete;
    public bool $confirmingProductoDeletion = false;

    public $nombre;
    public $categoriaId;
    public $costo;
    public $precio;
    public $porciento_descuento = 0.00;
    public $codigo;
    public $orden_visual;
    public $monedaId;

    public bool $atributosSugeridos = false;
    public bool $cardsDirty = false;

    public bool $editAtributosShow = false;
    public $productoIdEditAtributos = null;
    public ?Producto $productoEditAtributos = null;

    public $perPage = 10;
    public $formularioVisible = true;

    protected $rules = [
        'nombre'              => 'required|string|max:255',
        'categoriaId'         => 'required|exists:categorias,id',
        'localId'             => 'required|exists:locales,id',
        'monedaId'            => 'required|exists:monedas,id',
        'costo'               => 'required|numeric|min:0',
        'precio'              => 'required|numeric|min:0',
        'porciento_descuento' => 'numeric|min:0',
        'imagenes.*'          => 'image|max:1024',
    ];

    protected $messages = [
        'nombre.required'             => 'El Nombre es obligatorio',
        'localId.required'            => 'El Local es obligatorio',
        'categoriaId.required'        => 'La Categoría es obligatoria',
        'monedaId.required'           => 'La Moneda es obligatoria',
        'costo.required'              => 'El costo es obligatorio',
        'costo.numeric'               => 'El costo tiene que ser un número mayor que cero',
        'precio.required'             => 'El precio es obligatorio',
        'precio.numeric'              => 'El precio tiene que ser un número mayor que cero',
        'porciento_descuento.numeric' => 'Tiene que ser un número mayor que cero',
        'porciento_descuento.min'     => 'Tiene que ser un número mayor que cero',
        'imagenes.image'              => 'Solo se admiten imágenes',
        'imagenes.max'                => 'Imagen muy grande mayor de 1 MB',
    ];

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
        // Cambiar de parent::reordenarMedias a método correcto del trait
        $this->reorderMediaPersisted($orderedIds);

        $this->dispatch('productoMediasReordenadas');
    }

    /* ================================
    Marcar primaria
    =============================== */
    public function marcarPrimariaProducto($mediaId)
    {
        // Actualiza en memoria y persiste
        $this->markPrimaryInMemory($mediaId);
        $this->setPrimaryPersisted($mediaId);
    }

    /* ================================
       MOUNT
    ================================ */

    public function mount($localId = null, $categoriaId = null)
    {
        $this->loadLocales($localId);
        $this->monedas = Moneda::orderByDesc('es_principal')->orderBy('nombre')->get();
    }

    public function loadLocales($localId = null)
    {
        $this->locales = Local::orderBy('nombre')->get();

        $this->localId = $localId && $this->locales->contains('id', $localId)
            ? $localId
            : ($this->locales->first()?->id ?? null);

        $this->loadCategorias();
    }

    public function loadCategorias()
    {
        $this->categorias = Categoria::when($this->localId, fn($q) => $q->where('local_id', $this->localId))
            ->orderBy('orden_visual')
            ->orderBy('nombre')
            ->get();
    }

    public function localChanged()
    {
        $this->resetPage();
        $this->categoriaId = null;
        $this->loadCategorias();
    }

    public function categoriaChanged()
    {
        $this->resetPage();
    }

    /* ================================
       CRUD Productos
    ================================ */

    public function save()
    {
        if (!$this->codigo) {
            $next = Producto::max('id') + 1;
            $this->codigo = 'PRD' . str_pad($next, 8, '0', STR_PAD_LEFT);
        }

        $this->validate();

        $data = [
            'nombre'              => $this->nombre,
            'categoria_id'        => $this->categoriaId,
            'moneda_id'           => $this->monedaId,
            'local_id'            => $this->localId,
            'costo'               => $this->costo,
            'precio'              => $this->precio,
            'porciento_descuento' => $this->porciento_descuento,
            'codigo'              => $this->codigo,
            'orden_visual'        => $this->orden_visual ?? (Producto::max('orden_visual') + 1),
        ];

        try {
            if ($this->productoId) {
                $producto = Producto::findOrFail($this->productoId);
                $producto->update($data);
            } else {
                $producto = Producto::create($data);
                $this->productoId = $producto->id;
            }

            $this->subirImagenes($producto);

        } catch (Throwable $e) {
            $this->logError('save', $e, ['producto_id' => $this->productoId]);
            return;
        }

        $this->dispatch('livewire:alert', [
            'message' => 'Producto guardado correctamente',
            'type'    => 'success',
        ]);

        $this->resetForm();
        $this->dispatch('productosActualizados');
    }

    #[On('edit')]
    public function edit($id)
    {
        try {
            $producto = Producto::findOrFail($id);

            $this->productoId = $producto->id;
            $this->categoriaId = $producto->categoria_id;
            $this->monedaId = $producto->moneda_id;
            $this->nombre = $producto->nombre;
            $this->costo = $producto->costo;
            $this->precio = $producto->precio;
            $this->porciento_descuento = $producto->porciento_descuento;
            $this->codigo = $producto->codigo;
            $this->orden_visual = $producto->orden_visual;

            $this->loadMedias($producto);

        } catch (Throwable $e) {
            $this->logError('edit', $e, ['producto_id' => $id]);
        }
    }

    #[On('confirmDelete')]
    public function confirmDelete($id)
    {
        $this->productoIdToDelete = $id;
        $this->confirmingProductoDeletion = true;
    }

    public function delete()
    {
        if (!$this->productoIdToDelete) return;

        try {
            Producto::findOrFail($this->productoIdToDelete)->delete();
        } catch (Throwable $e) {
            $this->logError('delete', $e, ['producto_id' => $this->productoIdToDelete]);
            return;
        }

        $this->confirmingProductoDeletion = false;
        $this->productoIdToDelete = null;

        $this->resetForm();
        $this->dispatch('productosActualizados');
    }

    public function cancel()
    {
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset([
            'productoId', 'nombre', 'categoriaId', 'costo', 'precio',
            'porciento_descuento', 'codigo', 'orden_visual', 'monedaId'
        ]);
        $this->resetMediaProperties();
        $this->resetValidation();
    }


    /* ================================
       Misc
    ================================ */

    #[On('atributos-sugeridos-updated')]
    public function actualizarTipoAtributos(bool $sugeridos)
    {
        $this->atributosSugeridos = $sugeridos;
    }

    #[On('cards-dirty-updated')]
    public function actualizarCardsDirty(bool $dirty)
    {
        $this->cardsDirty = $dirty;
    }

    public function tieneCambiosPendientes(): bool
    {
        return $this->atributosSugeridos || $this->cardsDirty;
    }

    public function generarBarcode()
    {
        if (!$this->codigo) {
            return null;
        }

        $generator = new BarcodeGeneratorPNG();

        return base64_encode(
            $generator->getBarcode(
                $this->codigo,
                $generator::TYPE_CODE_128
            )
        );
    }

    public function render()
    {
        return view('livewire.admin.productos')
            ->layout('layouts.app_admin');
    }
}
