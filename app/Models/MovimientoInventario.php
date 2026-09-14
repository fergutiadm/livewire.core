<?php

namespace App\Models;

use App\Models\Traits\HasTrazas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class MovimientoInventario extends Model
{
    use HasFactory, HasTrazas;

    protected $table = 'movimientos_inventarios';

    protected $fillable = [
        'tipo',
        'estado',
        'origen_local_id',
        'destino_local_id',
        'local_id',
        'fecha',
        'observacion',
    ];

    protected $casts = [
        'fecha' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    public function origenLocal(): BelongsTo
    {
        return $this->belongsTo(
            Local::class,
            'origen_local_id'
        );
    }

    public function destinoLocal(): BelongsTo
    {
        return $this->belongsTo(
            Local::class,
            'destino_local_id'
        );
    }

    public function local(): BelongsTo
    {
        return $this->belongsTo(
            Local::class,
            'local_id'
        );
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(
            MovimientoInventarioDetalle::class
        );
    }

    public function atributable(): MorphMany
    {
        return $this->morphMany(
            AtributableValoreMovimiento::class,
            'atributable'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Tipos
    |--------------------------------------------------------------------------
    */

    public const TIPO_ENTRADA = 'entrada';
    public const TIPO_SALIDA = 'salida';
    public const TIPO_TRASLADO = 'traslado';
    public const TIPO_AJUSTE = 'ajuste';

    /*
    |--------------------------------------------------------------------------
    | Estados
    |--------------------------------------------------------------------------
    */

    public const ESTADO_BORRADOR = 'borrador';
    public const ESTADO_CONFIRMADO = 'confirmado';
    public const ESTADO_ANULADO = 'anulado';

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeEntradas($query)
    {
        return $query->where('tipo', self::TIPO_ENTRADA);
    }

    public function scopeSalidas($query)
    {
        return $query->where('tipo', self::TIPO_SALIDA);
    }

    public function scopeTraslados($query)
    {
        return $query->where('tipo', self::TIPO_TRASLADO);
    }

    public function scopeAjustes($query)
    {
        return $query->where('tipo', self::TIPO_AJUSTE);
    }

    public function scopeBorradores($query)
    {
        return $query->where('estado', self::ESTADO_BORRADOR);
    }

    public function scopeConfirmados($query)
    {
        return $query->where('estado', self::ESTADO_CONFIRMADO);
    }

    public function scopeAnulados($query)
    {
        return $query->where('estado', self::ESTADO_ANULADO);
    }
}