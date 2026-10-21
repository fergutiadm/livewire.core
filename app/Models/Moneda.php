<?php

namespace App\Models;

use App\Models\Traits\HasTrazas;
use Illuminate\Database\Eloquent\Builder;
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

    public function gastos(): HasMany
    {
        return $this->hasMany(Gasto::class);
    }

    public function tipoGastos(): HasMany
    {
        return $this->hasMany(TipoGasto::class);
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

    public function cierres_caja_moneda(): HasMany
    {
        return $this->hasMany(CierreCajaMoneda::class);
    }

    public function denominaciones(): HasMany
    {
        return $this->hasMany(Denominacion::class);
    }

    /*
    public function atributables_valores_movimientos(): HasMany
    {
        return $this->hasMany(AtributablesValoresMovimientos::class);
    }
    */

    /* =====================
     |  SCOPES
     ===================== */

    public function scopeEsPrincipal($query): Builder
    {
        return $query->where('es_principal', true);
    }

    /**
     * Filtra solo las monedas que están activas.
     */
    public function scopeActivas($query): Builder
    {
        return $query->where('activa', true);
    }

    /**
     * Obtiene las monedas ordenadas priorizando la moneda principal y luego por código alfabético.
     */
    public function scopeOrdenadas($query): Builder
    {
        return $query
            ->orderBy('es_principal', 'desc')
            ->orderBy('codigo');
    }

    /**
     * Obtiene las monedas activas con los campos necesarios
     * ordenadas priorizando la moneda principal y luego por código alfabético.
     */
    public function scopeParaSelect($query): Builder
    {
        return $query->activas()
            ->select(
                'id',
                'codigo',
                'tasa_cambio',
                'es_principal'
            )
            ->ordenadas();
    }

    /**
     * Alias semántico para la barra de navegación (hereda el ordenamiento por defecto).
     */
    public function scopeParaBarra($query): Builder
    {
        return $query->paraSelect();
    }

    /**
     * COMPATIBILIDAD VARIANTE: Devuelve exactamente los mismos campos y filtros, 
     * pero SIN aplicar ningún tipo de ordenamiento.
     */
    public function scopeParaSelectSinOrden($query): Builder
    {
        return $query->activas()->select(
            'id',
            'codigo',
            'tasa_cambio',
            'es_principal'
        );
    }

}
