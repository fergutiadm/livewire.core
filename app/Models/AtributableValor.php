<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AtributableValor extends Model
{
    use HasFactory;

    protected $table = 'atributables_valores';

    protected $fillable = [
        'orden_visual',
        'atributo_valor_id',
        'atributable_id',
        'atributable_type',
    ];

    protected $casts = [
        'orden_visual' => 'integer',
    ];

    public function atributoValor(): BelongsTo
    {
        return $this->belongsTo(
            AtributoValor::class,
            'atributo_valor_id'
        );
    }

    public function atributable(): MorphTo
    {
        return $this->morphTo();
    }
}