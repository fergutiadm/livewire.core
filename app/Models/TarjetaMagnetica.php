<?php

namespace App\Models;

use App\Models\Traits\HasTrazas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TarjetaMagnetica extends Model
{

    protected $table = "tarjetas_magneticas";

    protected $fillable = [
                            'moneda_id',
                            'numero',
                            'propietario',
                            'color_bg',
                            'color_text',
                          ];

    use HasFactory, HasTrazas;

    protected bool $includeAllOnDelete = true;

    public function moneda()
    {
        return $this->belongsTo(Moneda::class);
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }

    /* =====================
     |  RELACIONES
     ===================== */

    public function ventas_producto()
    {
        return $this->hasMany(VentaProducto::class);
    }

    public function trazas_tarjetas_magneticas()
    {
        return $this->hasMany(TrazasTarjetasMagneticas::class);
    }

    public function movimientos_inventarios()
    {
        return $this->hasMany(MovimientoInventario::class);
    }

    /* =========================
     | ACCESORES
     =========================*/

     public function getmonedaCodigoAttribute()
     {
       return $this->moneda?->codigo??'--';
     }
}
