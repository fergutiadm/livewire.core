<?php

namespace App\Models\Traits;

use App\Models\Media;

trait HasMediaImages
{
    /* ===========================
    | RELACION BASE MEDIA
    =========================== */

    public function medias()
    {
        return $this->morphMany(Media::class, 'mediable')
            ->orderByDesc('is_primary')
            ->orderBy('orden_visual');
    }

    /* ===========================
    | PRIMARY MEDIA
    =========================== */

    public function getPrimaryMediaAttribute()
    {
        if ($this->relationLoaded('medias')) {
            return $this->medias
                ->firstWhere('media_type', Media::TYPE_PRIMARY)
                ?? $this->medias->first();
        }

        return $this->medias()
            ->where('media_type', Media::TYPE_PRIMARY)
            ->first()
            ?? $this->medias()->first();
    }

    /* ===========================
    | URL IMAGEN PRINCIPAL
    =========================== */

    public function getImagenUrlAttribute()
    {
        // soporte legacy si existe campo imagen_url
        if (!empty($this->attributes['imagen_url'])) {
            return $this->attributes['imagen_url'];
        }

        $media = $this->primary_media;

        if ($media) {
            // return asset('storage/' . $media->path);
            return asset($media->path);
        }

        // fallback placeholder dinámico si existe metodo
        if (method_exists($this, 'placeholderUrl')) {
            return $this->placeholderUrl();
        }

        return null;
    }

    /* ===========================
    | GALERIA
    =========================== */

    public function getGalleryMediaAttribute()
    {
        if ($this->relationLoaded('medias')) {
            return $this->medias;
        }

        return $this->medias()->get();
    }

    /* ===========================
    | THUMBNAIL (FUTURO POS 😏)
    =========================== */

    public function getThumbnailAttribute()
    {
        return $this->imagen_url;
    }

    public function getPosMediaAttribute()
    {
        return $this->medias()
            ->where('media_type', Media::TYPE_POS)
            ->first();
    }

    public function getThumbnailMediaAttribute()
    {
        return $this->medias()
            ->where('media_type', Media::TYPE_THUMBNAIL)
            ->first();
    }
}
