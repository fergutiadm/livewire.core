<?php

namespace App\Models;

use App\Models\Traits\HasTrazas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cliente extends Model
{
    use HasFactory, HasTrazas;

    protected bool $includeAllOnDelete = true;

    protected $fillable = [
        'nombre',
        'email',
        'telefono',
        'ci',
    ];

    // protected $casts = [
    //     'ci'  =>  'char'
    // ];

    /* =====================
     |  RELACIONES
     ===================== */
    public function ventas(): BelongsTo
    {
        return $this->belongsTo(Venta::class);
    }
}
