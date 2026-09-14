<?php

namespace App\Http\Livewire\Traits;

trait HasReorderableMedia
{
    public $medias = [];
    public $primaryImageId = null;

    /**
     * Reordenar medias **persistente** (actualiza DB)
     */
    public function reorderMediaPersisted(array $orderedIds)
    {
        foreach ($orderedIds as $index => $id) {
            $media = collect($this->medias)->firstWhere('id', $id);
            if ($media) {
                $media['orden_visual'] = $index + 1;
            }
        }

        $this->medias = collect($this->medias)
            ->sortBy('orden_visual')
            ->values()
            ->toArray();

        $this->persistMediaOrder();
    }

    /**
     * Establecer primaria **persistente** (actualiza DB)
     */
    public function setPrimaryPersisted($mediaId)
    {
        $this->primaryImageId = $mediaId;

        foreach ($this->medias as &$media) {
            $media['is_primary'] = $media['id'] == $mediaId;
        }

        $this->persistMediaOrder();
    }

    abstract protected function persistMediaOrder(): void;
}
