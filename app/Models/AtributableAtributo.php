<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AtributableAtributo extends Model
{
    use HasFactory;

    protected $table = 'atributables_atributos';

    protected $fillable = [
        'atributo_id',
        'atributable_type',
        'atributable_id',
        'orden_visual',
    ];

    /**
     * Polimórfica: el modelo que tiene este atributo
     */
    public function atributable()
    {
        return $this->morphTo();
    }

    /**
     * Relación con el modelo Atributo
     */
    public function atributo()
    {
        return $this->belongsTo(Atributo::class, 'atributo_id');
    }
}
