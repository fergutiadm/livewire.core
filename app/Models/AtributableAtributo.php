<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AtributableAtributo extends Model
{
    use HasFactory;

    protected $table = 'atributables_atributos';

    protected $fillable = [
        'atributo_id',
        'atributable_type',
        'atributable_id',
        'tipo',
        'obligatorio',
        'orden_visual',
        'activo',
    ];

    protected $casts = [
        'tipo' => 'string',
        'obligatorio' => 'boolean',
        'orden_visual' => 'integer',
        'activo' => 'boolean',
    ];

    public function atributable(): MorphTo
    {
        return $this->morphTo();
    }

    public function atributo(): BelongsTo
    {
        return $this->belongsTo(Atributo::class, 'atributo_id');
    }
}