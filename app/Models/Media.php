<?php

namespace App\Models;

use App\Models\Traits\HasTrazas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

use Illuminate\Support\Collection;

class Media extends Model
{
    use HasFactory, HasTrazas;

    const TYPE_PRIMARY   = 'primary';
    const TYPE_THUMBNAIL = 'thumbnail';
    const TYPE_POS       = 'pos';
    const TYPE_BANNER    = 'banner';
    const TYPE_GALLERY   = 'gallery';

    protected bool $includeAllOnDelete = true;

    protected array $trazaHidden = [
        'created_at',
        'updated_at',
    ];

    protected $table = "media";

    protected $fillable = [
        'path',
        'is_primary',
        'orden_visual',
        'mediable_id',
        'mediable_type',
    ];

    public function guardarAtributos(
        Collection $atributos,
        string $modelType,
        int $modelId
    ): void
    {
        if ($atributos->isEmpty()) {
            return;
        }

        $orden = 1;

        foreach ($atributos as $atributoValorId) {
            AtributableValor::updateOrCreate(
                [
                    'atributo_valor_id' => $atributoValorId,
                    'atributable_type'  => $modelType,
                    'atributable_id'    => $modelId,
                ],
                [
                    'orden_visual' => $orden++,
                ]
            );
        }
    }


    /* =====================
    |  RELACIONES
    ===================== */

    /**
     * Valores reales del media
     */
    public function atributosValores()
    {
        return $this->morphToMany(
            AtributoValor::class,
            'atributable',
            'atributables_valores'
        );
    }

    public function mediable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Atributos usados por el producto
     */
    public function atributos()
    {
        return Atributo::whereHas('valores', function ($q) {
            $q->whereHas('media', function ($q2) {
                $q2->where('media.id', $this->id);
            });
        });
    }

    // --------------------
    // Accesor útil
    // --------------------

    /**
     * Agrupa los valores por el nombre del atributo
     * Útil para Blade/Livewire
     */
    public function getAtributosPorNombreAttribute()
    {
        return $this->atributosValores->groupBy(fn($valor) => $valor->atributo->nombre);
    }

}
