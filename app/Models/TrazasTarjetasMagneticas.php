<?php

namespace App\Models;

use App\Models\Traits\HasTrazas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrazasTarjetasMagneticas extends Model
{

    protected $table = "trazas_tarjetas_magneticas";

    use HasFactory, HasTrazas;

    protected bool $includeAllOnDelete = true;

    /* =====================
     |  RELACIONES
     ===================== */

    public function tarjeta_magnetica()
    {
        return $this->belongsTo(TarjetaMagnetica::class);
    }
}
