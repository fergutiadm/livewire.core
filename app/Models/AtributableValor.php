<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AtributableValor extends Model
{
    protected $table = 'atributables_valores';

    protected $fillable = [
        'atributo_valor_id',
        'atributable_id',
        'atributable_type',
        'orden_visual',
    ];

    public $timestamps = true;

    public function atributable(): MorphTo
    {
        return $this->morphTo();
    }

    public function valor()
    {
        return $this->belongsTo(AtributoValor::class, 'atributo_valor_id');
    }

    public function atributoValor()
    {
        return $this->belongsTo(AtributoValor::class, 'atributo_valor_id');
    }

    public function guardarCardsOrden(array $cards, string $modelType, int $modelId)
    {
        $orden = 1;
        foreach ($cards as $atributoId => $card) {
            foreach ($card['valores'] as $valorId => $valor) {
                AtributableValor::updateOrCreate(
                    [
                        'atributo_valor_id' => $valorId,
                        'atributable_type' => "App\\Models\\$modelType",
                        'atributable_id' => $modelId,
                    ],
                    [
                        'orden_visual' => $orden++
                    ]
                );
            }
        }
    }
}
