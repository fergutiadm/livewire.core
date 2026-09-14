<?php

namespace App\Modules\Inventario\Producto\Livewire;

use App\Core\CQRS\HasCommands;

use Livewire\Component;

use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Local;
use App\Models\Moneda;

use Illuminate\Support\Facades\Log;

use Livewire\Attributes\On;

use Override;

use Picqer\Barcode\BarcodeGeneratorPNG;

class ProductoForm extends Component
{
    use HasCommands;

    public ?int $productoId = null;

    public ?int $localId = null;

    public ?int $categoriaId = null;

    public ?int $monedaId = null;

    public string $nombre = '';

    public ?float $costo = null;

    public ?float $precio = null;

    public float $porciento_descuento = 0;

    public string $codigo = '';


    /*
    |--------------------------------------------------------------------------
    | Inicialización
    |--------------------------------------------------------------------------
    */

    public function mount(): void
    {
        $this->localId =
            Local::orderBy('nombre')
                ->value('id');

        $this->monedaId =
            Moneda::orderByDesc('es_principal')
                ->value('id');
    }


    /*
    |--------------------------------------------------------------------------
    | Validación
    |--------------------------------------------------------------------------
    */

    public function rules(): array
    {
        return [
            'nombre' =>
                'required|string|max:255',

            'localId' =>
                'required|exists:locales,id',

            'categoriaId' =>
                'required|exists:categorias,id',

            'monedaId' =>
                'required|exists:monedas,id',

            'costo' =>
                'required|numeric|min:0',

            'precio' =>
                'required|numeric|min:0',

            'porciento_descuento' =>
                'nullable|numeric|min:0',
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Mensajes
    |--------------------------------------------------------------------------
    */

    #[Override]
    public function getMessages(): array
    {
        return [
            'nombre.required' =>
                'El Nombre es obligatorio',

            'localId.required' =>
                'El Local es obligatorio',

            'categoriaId.required' =>
                'La Categoría es obligatoria',

            'monedaId.required' =>
                'La Moneda es obligatoria',

            'costo.required' =>
                'El costo es obligatorio',

            'costo.numeric' =>
                'El costo tiene que ser un número mayor que cero',

            'precio.required' =>
                'El precio es obligatorio',

            'precio.numeric' =>
                'El precio tiene que ser un número mayor que cero',

            'porciento_descuento.numeric' =>
                'Tiene que ser un número mayor que cero',

            'porciento_descuento.min' =>
                'Tiene que ser un número mayor que cero',

            /*
            |--------------------------------------------------------------------------
            | Estos mensajes se conservan por compatibilidad.
            |--------------------------------------------------------------------------
            |
            | La validación de imágenes ya no pertenece a ProductoForm,
            | pero no los eliminamos hasta confirmar que no existen
            | reglas heredadas que los utilicen.
            |
            */

            'imagenes.image' =>
                'Solo se admiten imágenes',

            'imagenes.max' =>
                'Imagen muy grande mayor de 1 MB',
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

    #[On('producto-edicion-cargado')]
    public function productoEdicionCargada(): void
    {
        $this->dispatch('loading-stop');
    }

    /*
    |--------------------------------------------------------------------------
    | Cambio de Local
    |--------------------------------------------------------------------------
    */

    public function localChanged(): void
    {
        $this->categoriaId = null;

        $this->dispatch(
            'producto-filtros-actualizados',
            localId: $this->localId,
            categoriaId: $this->categoriaId,
        );

        $this->dispatch('producto-media-reset');

        $this->resetForm();
    }


    /*
    |--------------------------------------------------------------------------
    | Cambio de Categoría
    |--------------------------------------------------------------------------
    */

    public function categoriaChanged(): void
    {
        $this->dispatch(
            'producto-filtros-actualizados',
            localId: $this->localId,
            categoriaId: $this->categoriaId,
        );

        $this->dispatch('producto-media-reset');

        $this->resetForm();
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

        if ($this->productoId) {

            $this->command(
                'producto.update',
                $this->payload()
            );

            /*
            |--------------------------------------------------------------------------
            | Informar al MediaManager
            |--------------------------------------------------------------------------
            */

            $this->dispatch(
                'producto-actualizado',
                productoId: $this->productoId
            );

            $this->dispatch(
                'livewire:alert',
                [
                    'message' =>
                        'Producto Actualizado correctamente...',
                    'type' =>
                        'success',
                ]
            );

        } else {

            /*
            |--------------------------------------------------------------------------
            | CREATE
            |--------------------------------------------------------------------------
            */

            $result = $this->command(
                'producto.create',
                $this->payload()
            );

            /*
            |--------------------------------------------------------------------------
            | IMPORTANTE
            |--------------------------------------------------------------------------
            |
            | El MediaManager necesita el ID recién creado.
            |
            | Aquí debemos obtenerlo del resultado del command.
            |
            */

            $productoId = $this->resolveCreatedProductoId(
                $result
            );

            if ($productoId) {

                $this->productoId =
                    $productoId;

                $this->dispatch(
                    'producto-creado',
                    productoId: $productoId
                );
            }

            $this->dispatch(
                'livewire:alert',
                [
                    'message' =>
                        'Producto Creado correctamente...',
                    'type' =>
                        'success',
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Avisar que el producto fue guardado
        |--------------------------------------------------------------------------
        */

        $this->dispatch(
            'producto-guardado'
        );

    }

    /*
    |--------------------------------------------------------------------------
    | Obtener ID del producto creado
    |--------------------------------------------------------------------------
    */

    private function resolveCreatedProductoId(
        mixed $result
    ): ?int {
        /*
        |--------------------------------------------------------------------------
        | Caso 1:
        | El command devuelve directamente un Producto.
        |--------------------------------------------------------------------------
        */

        if ($result instanceof Producto) {
            return $result->id;
        }

        /*
        |--------------------------------------------------------------------------
        | Caso 2:
        | El command devuelve un ID.
        |--------------------------------------------------------------------------
        */

        if (is_int($result)) {
            return $result;
        }

        /*
        |--------------------------------------------------------------------------
        | Caso 3:
        | El command devuelve un array.
        |--------------------------------------------------------------------------
        */

        if (is_array($result)) {

            if (
                isset($result['id']) &&
                is_numeric($result['id'])
            ) {
                return (int) $result['id'];
            }

            if (
                isset($result['productoId']) &&
                is_numeric($result['productoId'])
            ) {
                return (int) $result['productoId'];
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Caso 4:
        | El command devuelve un objeto con id.
        |--------------------------------------------------------------------------
        */

        if (
            is_object($result) &&
            isset($result->id)
        ) {
            return (int) $result->id;
        }

        return null;
    }


    /*
    |--------------------------------------------------------------------------
    | Editar
    |--------------------------------------------------------------------------
    */

    #[On('producto-cargar-edicion')]
    public function edit(int $id): void
    {
        $producto =
            Producto::findOrFail($id);

        $this->productoId =
            $producto->id;

        $this->localId =
            $producto->local_id;

        $this->categoriaId =
            $producto->categoria_id;

        $this->monedaId =
            $producto->moneda_id;

        $this->nombre =
            $producto->nombre;

        $this->costo =
            $producto->costo;

        $this->precio =
            $producto->precio;

        $this->porciento_descuento =
            $producto->porciento_descuento;

        $this->codigo =
            $producto->codigo;


        /*
        |--------------------------------------------------------------------------
        | Cargar galería
        |--------------------------------------------------------------------------
        |
        | ProductoForm no carga ninguna Media.
        |
        | Solamente comunica el ID al MediaManager.
        |
        */

        $this->dispatch(
            'producto-media-cargar',
            productoId: $producto->id
        );

        $this->dispatch(
            'producto-edicion-cargado',
            productoId: $producto->id
        );

        $this->resetValidation();
    }


    /*
    |--------------------------------------------------------------------------
    | Payload CQRS
    |--------------------------------------------------------------------------
    */

    private function payload(): array
    {
        return [
            'id' =>
                $this->productoId,

            'nombre' =>
                $this->nombre,

            'local_id' =>
                $this->localId,

            'categoria_id' =>
                $this->categoriaId,

            'moneda_id' =>
                $this->monedaId,

            'costo' =>
                $this->costo,

            'precio' =>
                $this->precio,

            'porciento_descuento' =>
                $this->porciento_descuento,

            'codigo' =>
                $this->codigo,
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
            'producto-media-reset'
        );

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
    | Reset por evento
    |--------------------------------------------------------------------------
    */

    #[On('reset-form')]
    public function resetFormEvent(): void
    {
        Log::info(
            'Listener reset-form',
            [
                '$this->productoId' =>
                    $this->productoId,
            ]
        );

        $this->resetForm();

        $this->dispatch(
            'producto-media-reset'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Barcode
    |--------------------------------------------------------------------------
    */

    public function generarBarcode()
    {
        if (!$this->codigo) {
            return null;
        }

        $generator =
            new BarcodeGeneratorPNG();

        return base64_encode(
            $generator->getBarcode(
                $this->codigo,
                $generator::TYPE_CODE_128
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Reset interno
    |--------------------------------------------------------------------------
    */

    private function resetForm(): void
    {
        $this->reset([
            'productoId',
            'nombre',
            // 'categoriaId',
            'costo',
            'precio',
            'porciento_descuento',
            'codigo',
        ]);

        $this->resetValidation();
    }

    #[On('media-procesada')]
    public function productoMediaProcesada(): void
    {
        $this->dispatch(
            'producto-media-reset'
        )->to(ProductoMediaManager::class);

        $this->resetForm();
    }

    /*
    |--------------------------------------------------------------------------
    | Descargar Barcode
    |--------------------------------------------------------------------------
    */

    public function descargarBarcode()
    {
        if (!$this->codigo) {
            return;
        }

        return redirect()->route(
            'barcode.preview',
            [
                'code' =>
                    $this->codigo,
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Ver ZPL
    |--------------------------------------------------------------------------
    */

    public function verCodigoZpl()
    {
        if (!$this->codigo) {
            return;
        }

        return redirect()->route(
            'barcode.zpl',
            [
                'code' =>
                    $this->codigo,

                'name' =>
                    $this->nombre,
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Render
    |--------------------------------------------------------------------------
    */

    public function render()
    {
        $locales =
            Local::orderBy('nombre')
                ->get();

        $categorias =
            $this->localId
                ? Categoria::query()
                    ->where(
                        'local_id',
                        $this->localId
                    )
                    ->orderBy('orden_visual')
                    ->orderBy('nombre')
                    ->get()
                : collect();

        $monedas =
            Moneda::orderByDesc(
                'es_principal'
            )->get();

        return view(
            'modules.inventario.producto.form',
            [
                'locales' =>
                    $locales,

                'categorias' =>
                    $categorias,

                'monedas' =>
                    $monedas,
            ]
        );
    }
}
