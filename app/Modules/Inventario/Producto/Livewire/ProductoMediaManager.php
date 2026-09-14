<?php

namespace App\Modules\Inventario\Producto\Livewire;

use App\Models\Media;
use App\Models\Producto;
use App\Http\Livewire\Traits\HandlesMediaUploads;
use App\Http\Livewire\Traits\HasReorderableMedia;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProductoMediaManager extends Component
{
    use WithFileUploads;
    use HandlesMediaUploads;
    use HasReorderableMedia;

    public ?int $productoId = null;

    /*
    |--------------------------------------------------------------------------
    | Inicialización
    |--------------------------------------------------------------------------
    */

    public function mount(?int $productoId = null): void
    {
        $this->productoId = $productoId;

        if ($this->productoId) {
            $this->loadMedias();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Cargar galería
    |--------------------------------------------------------------------------
    */

    public function loadMedias($model = null): void
    {
        if (
            $model === null &&
            $this->productoId
        ) {
            $model = Producto::find(
                $this->productoId
            );
        }

        if (!$model) {
            $this->medias = [];
            $this->primaryImageId = null;
            $this->mostrarUpload = false;

            return;
        }

        $this->productoId = $model->id;

        $this->medias = $model
            ->medias()
            ->get()
            ->toArray();

        $primary = collect($this->medias)
            ->firstWhere(
                'is_primary',
                1
            );

        $this->primaryImageId =
            $primary['id'] ?? null;

        $this->mostrarUpload = true;

        // Log::info('ProductoMediaManager::loadMedias FIN', [
        //     'productoId' => $this->productoId,
        //     'medias_count' => count($this->medias),
        //     'medias_ids' => collect($this->medias)->pluck('id')->values()->all(),
        //     'primaryImageId' => $this->primaryImageId,
        // ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Eliminar imagen temporal
    |--------------------------------------------------------------------------
    */

    public function removeImage(
        int $index
    ): void {
        unset(
            $this->imagenes[$index]
        );

        $this->imagenes =
            array_values(
                $this->imagenes
            );

        if (
            $this->primaryImageId ===
            "new-{$index}"
        ) {
            $this->primaryImageId = null;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Marcar Media para eliminar
    |--------------------------------------------------------------------------
    */

    public function marcarParaEliminar(
        int $mediaId
    ): void {
        $this->markMediaForDeletion(
            $mediaId
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Reordenar Media
    |--------------------------------------------------------------------------
    */

    public function reordenarMedias(
        array $orderedIds
    ): void {
        if (!$this->productoId) {
            return;
        }

        $producto = Producto::find(
            $this->productoId
        );

        if (!$producto) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Actualizar estado Livewire
        |--------------------------------------------------------------------------
        */

        $this->medias = collect(
            $this->medias
        )
            ->sortBy(
                function ($media) use (
                    $orderedIds
                ) {
                    $position = array_search(
                        (string) $media['id'],
                        array_map(
                            'strval',
                            $orderedIds
                        ),
                        true
                    );

                    return $position === false
                        ? PHP_INT_MAX
                        : $position;
                }
            )
            ->values()
            ->toArray();

        /*
        |--------------------------------------------------------------------------
        | Persistencia
        |--------------------------------------------------------------------------
        */

        foreach (
            $orderedIds as $index => $mediaId
        ) {
            $producto
                ->medias()
                ->where(
                    'id',
                    $mediaId
                )
                ->update([
                    'orden_visual' =>
                        $index + 1,
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Recargar
        |--------------------------------------------------------------------------
        */

        $this->loadMedias(
            $producto->fresh()
        );

        $this->dispatch(
            'mediasReordenadas'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Marcar principal
    |--------------------------------------------------------------------------
    */

    public function marcarPrimariaProducto(
        int|string $mediaId
    ): void {
        if (!$this->productoId) {
            return;
        }

        $producto = Producto::find(
            $this->productoId
        );

        if (!$producto) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Seguridad:
        |
        | La Media debe pertenecer al producto.
        |--------------------------------------------------------------------------
        */

        $media = $producto
            ->medias()
            ->where(
                'id',
                $mediaId
            )
            ->first();

        if (!$media) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Una sola primaria
        |--------------------------------------------------------------------------
        */

        $producto
            ->medias()
            ->update([
                'is_primary' => 0,
            ]);

        $media->update([
            'is_primary' => 1,
        ]);

        $this->loadMedias(
            $producto->fresh()
        );
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    #[On('producto-creado')]
    public function productoCreado(
        int $productoId
    ): void {
        $this->productoId =
            $productoId;

        $producto = Producto::find(
            $productoId
        );

        if (!$producto) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Procesar imágenes seleccionadas
        | después de crear el producto
        |--------------------------------------------------------------------------
        |
        | Las imágenes viven en este componente,
        | no en ProductoForm.
        |
        */

        $this->subirImagenes(
            $producto
        );

        /*
        |--------------------------------------------------------------------------
        | Recargar galería
        |--------------------------------------------------------------------------
        */

        $this->loadMedias(
            $producto->fresh()
        );

        $this->dispatch('media-procesada');
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    #[On('producto-actualizado')]
    public function productoActualizado(
        int $productoId
    ): void {
        $this->productoId =
            $productoId;

        $producto = Producto::find(
            $productoId
        );

        if (!$producto) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Procesar:
        |
        | - nuevas imágenes
        | - imágenes marcadas para eliminar
        | - primaria
        |--------------------------------------------------------------------------
        */

        $this->subirImagenes(
            $producto
        );

        /*
        |--------------------------------------------------------------------------
        | Recargar galería
        |--------------------------------------------------------------------------
        */

        $this->loadMedias(
            $producto->fresh()
        );

        $this->dispatch('media-procesada');
    }

    /*
    |--------------------------------------------------------------------------
    | Cargar Producto explícitamente
    |--------------------------------------------------------------------------
    */

    #[On('producto-media-cargar')]
    public function cargarProductoMedia(
        ?int $productoId = null
    ): void {

        $this->productoId = $productoId;

        $this->imagenes = [];
        $this->deleteMediaIds = [];
        $this->primaryImageId = null;

        $this->loadMedias();

        // Log::info('ProductoMediaManager::cargarProductoMedia FIN', [
        //     'productoId_final' => $this->productoId,
        //     'medias_count' => count($this->medias),
        //     'medias_ids' => collect($this->medias)->pluck('id')->values()->all(),
        // ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Reset
    |--------------------------------------------------------------------------
    */

    #[On('producto-media-reset')]
    public function resetMedia(): void
    {
        $this->productoId = null;

        $this->imagenes = [];
        $this->medias = [];
        $this->deleteMediaIds = [];
        $this->primaryImageId = null;

        $this->mostrarUpload = false;
    }

    /*
    |--------------------------------------------------------------------------
    | Persistir orden/principal
    |--------------------------------------------------------------------------
    */

    protected function persistMediaOrder(): void
    {
        if (!$this->productoId) {
            return;
        }

        foreach (
            $this->medias as $media
        ) {
            if (!isset($media['id'])) {
                continue;
            }

            Media::where(
                'id',
                $media['id']
            )->update([
                'orden_visual' =>
                    $media['orden_visual']
                    ?? null,

                'is_primary' =>
                    $media['is_primary']
                    ?? false,
            ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Render
    |--------------------------------------------------------------------------
    */

    public function render()
    {
        return view(
            'modules.inventario.producto.media-manager'
        );
    }
}
