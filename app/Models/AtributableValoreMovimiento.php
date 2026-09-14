<?php

namespace App\Models;

use App\Models\Traits\HasTrazas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AtributableValoreMovimiento extends Model
{
    protected $table = "atributables_valores_movimientos";

    use HasFactory, HasTrazas;

    protected bool $includeAllOnDelete = true;

    protected $fillable = [
        'moneda_id',
        'tasa_cambio',
        'cantidad',
        'costo',
        'precio',
    ];

    // protected $casts = [];

    public function atributable(): MorphTo
    {
        return $this->morphTo();
    }

    public function moneda(): BelongsTo
    {
        return $this->belongsTo(Moneda::class);
    }

    /* =====================
     |  RELACIONES
     ===================== */

    public function trazable(): MorphTo
    {
        return $this->morphTo();
    }

    public function trazas(): MorphMany
    {
        return $this->morphMany(Traza::class, 'trazable');
    }
}
