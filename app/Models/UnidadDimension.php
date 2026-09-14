<?php

namespace App\Models;

use App\Models\Traits\HasTrazas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UnidadDimension extends Model
{
    use HasFactory, HasTrazas;

    protected $table = 'unidad_dimensiones';

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function unidades(): HasMany
    {
        return $this->hasMany(UnidadMedida::class);
    }
}