<?php

namespace App\Models;

use App\Models\Traits\HasTrazas;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Stock extends Model
{
    use HasFactory, HasTrazas;

    protected $table = 'stocks';

    protected $fillable = [
        'local_id',
        'producto_id',
        'atributo_valor_id',
        'cantidad',
    ];

    protected $casts = [
        'cantidad' => 'decimal:4',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    public function local()
    {
        return $this->belongsTo(Local::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function atributoValor()
    {
        return $this->belongsTo(AtributoValor::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes útiles (te salvarán después 😄)
    |--------------------------------------------------------------------------
    */

    public function scopeDelProducto($query, $productoId)
    {
        return $query->where('producto_id', $productoId);
    }

    public function scopeDelLocal($query, $localId)
    {
        return $query->where('local_id', $localId);
    }

    public function scopeDeVariante($query, $atributoValorId)
    {
        return $query->where('atributo_valor_id', $atributoValorId);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers futuros ERP
    |--------------------------------------------------------------------------
    */

    public function esVariante(): bool
    {
        return !is_null($this->atributo_valor_id);
    }

    public function tieneStock(): bool
    {
        return $this->cantidad > 0;
    }
}
