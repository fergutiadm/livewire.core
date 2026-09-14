<?php

namespace App\Modules\Inventario\Categoria\Livewire;

use App\Models\Categoria;
use App\Models\Media;
use App\Http\Livewire\Traits\HandlesMediaUploads;
use App\Http\Livewire\Traits\HasReorderableMedia;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;

class CategoriaMediaManager extends Component
{
    use WithFileUploads;
    use HandlesMediaUploads;
    use HasReorderableMedia;

    public ?int $categoriaId = null;

    /*
    |--------------------------------------------------------------------------
    | Inicialización
    |--------------------------------------------------------------------------
    */

    public function mount(?int $categoriaId = null): void
    {

        $this->categoriaId = $categoriaId;

        if ($this->categoriaId) {
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
            $this->categoriaId
        ) {
            $model = Categoria::find(
                $this->categoriaId
            );
        }

        if (!$model) {

            $this->medias = [];
            $this->primaryImageId = null;
            $this->mostrarUpload = false;

            return;
        }

        $this->categoriaId = $model->id;

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
        if (!$this->categoriaId) {
            return;
        }

        $categoria = Categoria::find(
            $this->categoriaId
        );

        if (!$categoria) {
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
            $categoria
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
            $categoria->fresh()
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

    public function marcarPrimariaCategoria(
        int|string $mediaId
    ): void {
        if (!$this->categoriaId) {
            return;
        }

        $categoria = Categoria::find(
            $this->categoriaId
        );

        if (!$categoria) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Seguridad:
        | la Media debe pertenecer a la categoría.
        |--------------------------------------------------------------------------
        */

        $media = $categoria
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

        $categoria
            ->medias()
            ->update([
                'is_primary' => 0,
            ]);

        $media->update([
            'is_primary' => 1,
        ]);

        $this->loadMedias(
            $categoria->fresh()
        );
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    #[On('categoria-creada')]
    public function categoriaCreada(
        int $categoriaId
    ): void {

        $this->categoriaId =
            $categoriaId;

        $categoria = Categoria::find(
            $categoriaId
        );

        if (!$categoria) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Procesar imágenes seleccionadas antes del guardado
        |--------------------------------------------------------------------------
        |
        | Las imágenes viven en este componente, no en CategoriaForm.
        |
        */

        $this->subirImagenes(
            $categoria
        );

        /*
        |--------------------------------------------------------------------------
        | Recargar galería
        |--------------------------------------------------------------------------
        */

        $this->loadMedias(
            $categoria->fresh()
        );

        $this->dispatch(
            'media-procesada'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    #[On('categoria-actualizada')]
    public function categoriaActualizada(
        int $categoriaId
    ): void {

        $this->categoriaId =
            $categoriaId;

        $categoria = Categoria::find(
            $categoriaId
        );

        if (!$categoria) {
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
            $categoria
        );

        /*
        |--------------------------------------------------------------------------
        | Recargar galería
        |--------------------------------------------------------------------------
        */

        $this->loadMedias(
            $categoria->fresh()
        );

        $this->dispatch(
            'media-procesada'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Cargar Categoría explícitamente
    |--------------------------------------------------------------------------
    */

    #[On('categoria-media-cargar')]
    public function cargarCategoriaMedia(
        ?int $categoriaId = null
    ): void {

        $this->categoriaId = $categoriaId;

        $this->imagenes = [];
        $this->deleteMediaIds = [];
        $this->primaryImageId = null;

        $this->loadMedias();
    }

    /*
    |--------------------------------------------------------------------------
    | Reset
    |--------------------------------------------------------------------------
    */

    #[On('categoria-media-reset')]
    public function resetMedia(): void
    {
        $this->categoriaId = null;

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
        if (!$this->categoriaId) {
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

    public function render()
    {

        return view(
            'modules.inventario.categoria.media-manager'
        );
    }
}
