<?php

namespace App\Modules\Inventario\UnidadMedida\Services;

use App\Models\UnidadMedida;

class ConversionUnidadResultado
{
    public function __construct(
        public readonly string $cantidadOriginal,
        public readonly UnidadMedida $unidadOrigen,
        public readonly string $factorAplicado,
        public readonly string $cantidadConvertida,
        public readonly UnidadMedida $unidadDestino,
    ) {
    }
}