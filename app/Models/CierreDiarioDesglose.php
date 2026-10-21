<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Traits\HasTrazas;

class CierreDiarioDesglose extends Model
{
    use HasFactory, HasTrazas;

    protected $table = 'cierre_diario_desgloses';

    protected $fillable = [
        'local_id',
        'periodo_contable_id',
        'operador_id',
        'monto_inicial_definido',
        'monto_desglosado_total',
        'fecha',
    ];

    public function detalles(): HasMany
    {
        return $this->hasMany(CierreDetalle::class, 'cierre_diario_desglose_id');
    }
}