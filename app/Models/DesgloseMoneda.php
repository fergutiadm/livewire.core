<?php

namespace App\Models;

use App\Models\Traits\HasTrazas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class DesgloseMoneda extends Model
{
    use HasFactory, HasTrazas;

    protected bool $includeAllOnDelete = true;

    protected $table = "desgloses_monedas";

    protected $fillable = [
        'moneda_id',
        'precio',
        'fecha',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];


    /* =====================
     |  RELACIONES
     ===================== */

    public function moneda(): BelongsTo
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
