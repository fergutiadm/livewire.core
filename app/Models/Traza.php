<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Traza extends Model
{
    protected $table = 'trazas';

    protected $fillable = [
        'user_id',
        'operacion',
        'data',
        'ip_var',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    /* =====================
     |  ACCESORS
     ===================== */

    public function getFechaAttribute()
    {
        return $this->created_at;
    }

    /* =====================
     |  RELACIONES
     ===================== */

    public function trazable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /* =====================
     |  SCOPES
     ===================== */

    public function scopeOperacion($query, string $operacion)
    {
        return $query->where('operacion', $operacion);
    }

    public function scopeEntreFechas($query, $desde, $hasta)
    {
        return $query->whereBetween('fecha', [$desde, $hasta]);
    }
}
