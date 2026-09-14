<?php

namespace App\Http\Livewire\Traits;

use App\Models\Media;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

trait HandlesMediaUploads
{
    public $imagenes = [];
    public $medias = [];
    public $deleteMediaIds = [];
    public $primaryImageId = null;
    public $mostrarUpload = false;

    /**
     * Subir nuevas imágenes, asignar primaria y eliminar marcadas
     */
    protected function subirImagenes($model)
    {
        // Log::info('HandlesMediaUploads::subirImagenes - INIT', [
        //     'model' => get_class($model),
        //     'model_id' => $model->id,
        //     'imagenes_count' => count($this->imagenes),
        //     'primaryImageId' => $this->primaryImageId,
        //     ]);

        if (!$model) return;

        $primaryOriginalId = $model->medias()->where('is_primary', 1)->value('id');

        $model->medias()->update(['is_primary' => 0]);

        try {
            if ($this->imagenes) {
                $maxOrden = $model->medias()->max('orden_visual') ?? 0;

                foreach ($this->imagenes as $i => $file) {
                    $path = $file->store(class_basename($model), 'public');
                    $isPrimary = ($this->primaryImageId === "new-{$i}");

                    // Log::info('HandlesMediaUploads::subirImagenes', [
                    //     'model' => get_class($model),
                    //     'model_id' => $model->id,
                    //     'imagenes_count' => count($this->imagenes),
                    //     'primaryImageId' => $this->primaryImageId,
                    //     ]);

                    $media = Media::create([
                        'mediable_type' => get_class($model),
                        'mediable_id'   => $model->id,
                        'path'          => 'storage/' . $path,
                        'is_primary'    => $isPrimary,
                        'orden_visual'  => ++$maxOrden,
                    ]);

                    if (method_exists($model, 'atributosValores')) {
                        $atributos = $model->atributosValores()->pluck('atributo_valor_id');
                        $media->guardarAtributos($atributos, get_class($model), $media->id);
                    }
                }
            } elseif ($this->primaryImageId) {
                $media = $model->medias([$this->primaryImageId])->first();
                if ($media) $media->update(['is_primary' => 1]);
            }
        } catch (Throwable $e) {
            $this->logError('subirImagenes: update', $e, ['primaryImageId' => $this->primaryImageId]);
        }

        try {
            if ($this->deleteMediaIds) {
                $mediasDelete = Media::whereIn('id', $this->deleteMediaIds)->get();
                foreach ($mediasDelete as $media) {
                    $path = str_replace('storage/', '', $media->path);
                    Storage::disk('public')->delete($path);
                    $media->delete();
                }
            }
        } catch (Throwable $e) {
            $this->logError('subirImagenes: delete', $e, ['deleteMediaIds' => $this->deleteMediaIds]);
        }

        $this->resetMediaProperties();

        // Asegurar que haya una primaria
        if (!$model->medias()->where('is_primary', 1)->exists()) {
            if ($primaryOriginalId) {
                $original = $model->medias([$primaryOriginalId])->first();
                if ($original) $original->update(['is_primary' => 1]);
            }
            if (!$model->medias()->where('is_primary', 1)->exists()) {
                $first = $model->medias()->orderBy('orden_visual')->first();
                if ($first) $first->update(['is_primary' => 1]);
            }
        }

        $this->loadMedias($model);
    }

    public function loadMedias($model)
    {
        if (!$model) {
            $this->medias = [];
            return;
        }
        $this->medias = $model->medias()->orderBy('orden_visual')->get()->toArray();
        // $this->mostrarUpload = (bool) $model->id;
    }

    public function markMediaForDeletion($mediaId)
    {
        if (!in_array($mediaId, $this->deleteMediaIds)) {
            $this->deleteMediaIds[] = $mediaId;
        }

        $this->medias = collect($this->medias)
            ->reject(fn($m) => $m['id'] == $mediaId)
            ->values()
            ->toArray();
    }

    /**
     * Marcar como primaria **solo en memoria**
     */
    public function markPrimaryInMemory($mediaId)
    {
        $this->medias = collect($this->medias)
            ->transform(fn($item) => array_merge($item, ['is_primary' => ($item['id'] == $mediaId ? 1 : 0)]))
            ->toArray();

        $this->primaryImageId = $mediaId;
    }

    protected function resetMediaProperties()
    {
        $this->reset([
            'imagenes',
            'deleteMediaIds',
            'primaryImageId',
            'mostrarUpload',
        ]);
    }

    protected function logError(string $context, Throwable $e, array $extra = [])
    {
        Log::error("Livewire HandlesMediaUploads - {$context}", [
            'exception' => $e,
            'extra'     => $extra,
        ]);
    }
}
