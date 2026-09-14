<?php

namespace App\Models;

use App\Models\Traits\HasTrazas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class DesgloseTarjeta extends Model
{

    use HasFactory, HasTrazas;

    protected bool $includeAllOnDelete = true;

    protected $table = "desgloses_tarjetas";

    protected $fillable = [
        'tarjeta_id',
        'precio',
        'fecha',
    ];

    protected $casts = [
        'fecha'  =>  'date',
    ];


    /* =====================
     |  RELACIONES
     ===================== */

    public function tarjeta_magnetica(): BelongsTo
    {
        return $this->belongsTo(Moneda::class);
    }

    public function desglosable(): MorphTo
    {
        return $this->morphTo();
    }

    public function trazas(): MorphMany
    {
        return $this->morphMany(Traza::class, 'trazable');
    }
}
