<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SetupConfig extends Model
{
    public const TIPO_NUMERO    = 1;
    public const TIPO_TEXTO     = 2;
    public const TIPO_ARREGLO   = 3;

    protected $fillable = [
        'nombre',
        'item_id',
        'descripcion',
        'tipo',
        'valor',
        'valor_inicial',
        'valor_minimo',
        'valor_maximo',
    ];

    protected function casts(): array
    {
        return [
            'tipo' => 'integer',
        ];
    }
}
