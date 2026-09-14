<?php

namespace App\Models;

use App\Models\Traits\HasTrazas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Moneda extends Model
{
    use HasFactory, HasTrazas;

    protected bool $includeAllOnDelete = true;

    protected $table = 'monedas';

    protected $fillable = [
        'codigo',
        'nombre',
        'simbolo',
        'es_principal',
        'tasa_cambio',
        'activa',
        'color_bg',
        'color_text',
    ];

    protected $casts = [
        'es_principal' => 'boolean',
        'activa' => 'boolean',
        'tasa_cambio' => 'float',
    ];

    /* =====================
     |  RELACIONES
     ===================== */

    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class);
    }

    public function tarjetas_magneticas(): HasMany
    {
        return $this->hasMany(TarjetaMagnetica::class);
    }

    public function desgloses_monedas(): MorphMany
    {
        return $this->morphMany(DesgloseMoneda::class, 'desglosable');
    }

    public function movimientos_inventarios(): HasMany
    {
        return $this->hasMany(MovimientoInventario::class);
    }

    public function ventas(): HasMany
    {
        return $this->hasMany(Venta::class);
    }

    public function trazas_monedas(): HasMany
    {
        return $this->hasMany(TrazasMonedas::class);
    }

    public function ventas_productos(): HasMany
    {
        return $this->hasMany(VentaProducto::class);
    }

    public function atributables_valores_movimientos(): HasMany
    {
        return $this->hasMany(AtributablesValoresMovimientos::class);
    }

    /* =====================
     |  SCOPES
     ===================== */

    public function scopeActivas($query)
    {
        return $query->where('activa', true);
    }

    public function scopeParaBarra($query)
    {
        return $query->select(
            'codigo',
            'tasa_cambio',
            'es_principal'
        );
    }

}
