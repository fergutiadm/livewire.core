<?php

namespace App\Http\Livewire\Traits;

trait HasSortableMedia
{
    public $medias = [];
    public $primaryImageId = null;

    /**
     * Reordenar medias en memoria (no persiste automáticamente)
     */
    public function reordenarMedias($orderedIds)
    {
        foreach ($orderedIds as $index => $id) {
            $media = collect($this->medias)->firstWhere('id', $id);
            if ($media) {
                $media['orden'] = $index + 1;
            }
        }

        $this->medias = collect($this->medias)
            ->sortBy('orden')
            ->values()
            ->toArray();
    }

    /**
     * Establecer primaria en memoria (no persiste)
     */
    public function setPrimary($mediaId)
    {
        $this->primaryImageId = $mediaId;

        foreach ($this->medias as &$media) {
            $media['is_primary'] = $media['id'] == $mediaId;
        }
    }
}
