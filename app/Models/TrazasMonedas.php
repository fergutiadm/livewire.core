<?php

namespace App\Models;

use App\Models\Traits\HasTrazas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrazasMonedas extends Model
{

    protected $table = "trazas_monedas";

    use HasFactory, HasTrazas;

    protected bool $includeAllOnDelete = true;

    /* =====================
     |  RELACIONES
     ===================== */

    public function moneda()
    {
        return $this->belongsTo(Moneda::class);
    }
}
