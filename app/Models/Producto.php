<?php

namespace App\Models;

use App\Models\Traits\HasMediaImages;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\Traits\HasTrazas;

class Producto extends Model
{
    use HasFactory, HasTrazas, HasMediaImages;

    protected bool $includeAllOnDelete = true;

    protected array $trazaHidden = [
        'imagenes', // opcional: no guardar imágenes en la traza
        'costo',    // opcional: datos sensibles
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'local_id',
        'categoria_id',
        'moneda_id',
        'unidad_medida_base_id',
        'permite_fraccion_base',
        'nombre',
        'descripcion',
        'imagen_minimalista',
        'icono',
        'color_bg',
        'color_text',
        'orden_visual',
        'porciento_descuento',
        'costo',
        'precio',
        'codigo'
    ];

    protected $casts = [
        'imagenes' => 'array',
        'permite_fraccion_base' => 'boolean',
    ];

    public function placeholderUrl()
    {
        return route('placeholder.producto', ['id',  $this->id]);
    }

    /* =====================
     |  RELACIONES
     ===================== */

    public function local(): BelongsTo
    {
        return $this->belongsTo(Local::class);
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    public function moneda(): BelongsTo
    {
        return $this->belongsTo(Moneda::class);
    }

    public function venta_producto(): HasMany
    {
        return $this->hasMany(VentaProducto::class);
    }

    public function unidadMedidaBase(): BelongsTo
    {
        return $this->belongsTo(
            UnidadMedida::class,
            'unidad_medida_base_id'
        );
    }

    public function unidades(): HasMany
    {
        return $this->hasMany(ProductoUnidad::class);
    }


    /**
     * Valores reales del producto
     */
    public function atributosValores()
    {
        return $this->morphToMany(
            AtributoValor::class,
            'atributable',
            'atributables_valores'
        )->withPivot('orden_visual')
         ->orderBy('atributables_valores.orden_visual');
    }

    /**
     * Medias reales del producto
     * @param array $relatedKeys Opcional: filtrar por IDs específicos de Media
     */
    public function medias(array $relatedKeys = [])
    {
        // Relación polimórfica correcta
        $query = $this->morphMany(Media::class, 'mediable');

        // Filtrar por IDs si se proporcionan
        if (!empty($relatedKeys)) {
            $query->whereIn('id', $relatedKeys);
        }

        // Orden por primaria primero
        return $query->orderByDesc('is_primary')->orderBy('orden_visual');
    }

    /**
     * Atributos usados por el producto
     */
    public function atributos()
    {
        return Atributo::whereHas('valores', function ($q) {
            $q->whereHas('productos', function ($q2) {
                $q2->where('productos.id', $this->id);
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
