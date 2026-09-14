<?php

namespace App\Models;

use App\Models\Traits\HasTrazas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Existencia extends Model
{
    use HasFactory, HasTrazas;

    protected $table = 'existencias';

    protected $fillable = [
        'producto_id',
        'local_id',
        'cantidad_base',
    ];

    protected $casts = [
        'cantidad_base' => 'decimal:10',
    ];

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    public function local(): BelongsTo
    {
        return $this->belongsTo(Local::class);
    }
}