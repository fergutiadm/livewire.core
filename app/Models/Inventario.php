<?php

namespace App\Models;

use App\Models\Traits\HasTrazas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inventario extends Model
{
    use HasFactory, HasTrazas;

    protected bool $includeAllOnDelete = true;

    public const ESTADO_BORRADOR   = 'borrador';
    public const ESTADO_CONFIRMADO = 'confirmado';
    public const ESTADO_CANCELADO  = 'cancelado';
    public const ESTADO_CERRADO    = 'cerrado';

    public const TIPO_ENTRADA  = 'entrada';
    public const TIPO_SALIDA   = 'salida';
    public const TIPO_TRASLADO = 'traslado';
    public const TIPO_AJUSTE   = 'ajuste';


    protected $fillable = [
        'user_id',
        'origen',
        'destino',
        'fecha',
        'descripcion',
        'pendiente',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    /* =====================
     |  RELACIONES
     ===================== */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function moneda(): BelongsTo
    {
        return $this->belongsTo(Moneda::class);
    }

    public function origen(): BelongsTo
    {
        return $this->belongsTo(Local::class);
    }

    public function movimientos_inventarios(): HasMany
    {
        return $this->hasMany(MovimientoInventario::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function atributoValor()
    {
        return $this->belongsTo(AtributoValor::class);
    }

    public function local()
    {
        return $this->belongsTo(Local::class);
    }

    public function periodoContable()
    {
        return $this->belongsTo(PeriodoContable::class);
    }

    public function monedaBase()
    {
        return $this->belongsTo(Moneda::class,'moneda_base_id');
    }

    public function movimientos()
    {
        return $this->hasMany(MovimientoInventario::class);
    }


}
