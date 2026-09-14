<?php

namespace App\Models;

use App\Models\Traits\HasMediaImages;
use App\Models\Traits\HasTrazas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Services\PlaceholderService;

class Categoria extends Model
{
    use HasFactory, HasTrazas, HasMediaImages;

    protected bool $includeAllOnDelete = true;

    protected $fillable = [
        'local_id',
        'nombre',
        'descripcion',
        'orden_visual',
        'porciento_descuento',
        'imagen_minimalista',
        'icono',
        'icono_secundario',
        'color_bg',
        'color_text',
    ];

    public function placeholderUrl()
    {
        return route('placeholder.categoria', ['id',  $this->id]);
    }

    protected static function booted()
    {
        static::creating(function ($categoria) {
            // Orden visual automático
            if ($categoria->orden_visual === null) {
                $categoria->orden_visual =
                    self::where('local_id', $categoria->local_id)
                        ->max('orden_visual') + 1;
            }

            // Imagen minimalista automática
            if (empty($categoria->imagen_minimalista)) {
                // IMPORTANTE: aún no tiene ID porque es creating
                // Lo generamos pasando nombre y null, luego puedes regenerar con ID si necesitas
                $categoria->imagen_minimalista = PlaceholderService::generarCategoria($categoria->nombre, null);
            }
        });

        static::created(function ($categoria) {
            if (empty($categoria->imagen_minimalista)) {
                $categoria->imagen_minimalista = PlaceholderService::generarCategoria($categoria->nombre, $categoria->id);
                $categoria->saveQuietly(); // evita loop
            }
        });
    }

    /**
     * Atributos (derivados desde valores)
     */
    public function atributos()
    {
        return Atributo::whereHas('valores', function ($q) {
            $q->whereHas('categorias', function ($q2) {
                $q2->where('categorias.id', $this->id);
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

    /* =====================
     |  RELACIONES
     ===================== */

    public function local(): BelongsTo
    {
        return $this->belongsTo(Local::class);
    }

    /**
     * Valores por defecto de la categoría
     */
    public function atributosValores()
    {
        return $this->morphToMany(
            AtributoValor::class,
            'atributable',
            'atributables_valores'
        );
    }

    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class);
    }

    /**
     * Medias reales de la categoria
     */
    public function medias(array $relatedKeys = [])
    {
        $query = $this->morphMany(Media::class, 'mediable');

        if (!empty($relatedKeys)) {
            $query->whereIn('id', $relatedKeys);
        }

        return $query
            ->orderByDesc('is_primary')
            ->orderBy('orden_visual');
    }


    /**
     * Asignar orden arbitrario Por Local
     * Esto para -- public function reorderCategorias($items) en componente Categorias--
     * Tomamos el mayor orden y a partir de este asignamos los demas
     */
    public static function ordenArbitrarioPorLocal($local_id)
    {
        $orden_visual = (self::where('local_id', $local_id)->max('orden_visual') ?? 0) + 1;
        $categorias = self::where('local_id', $local_id)->get();

        foreach($categorias as $item)
        {
            $item->orden_visual = ++$orden_visual;
            $item->save();
        }
    }
}
