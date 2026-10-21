<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CierreDetalle extends Model
{
    protected $table = 'cierre_detalles';

    protected $fillable = [
        'cierre_diario_desglose_id',
        'denominacion_id',
        'cantidad_billetes',
        'subtotal',
    ];

    public function denominacion(): BelongsTo
    {
        return $this->belongsTo(Denominacion::class, 'denominacion_id');
    }
}