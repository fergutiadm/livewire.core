<?php

namespace App\Modules\Finanzas\CierreCaja\DTOs;

class SaveCierreCajaMonedaDetallesDTO
{
    public function __construct(
        public readonly int $cierreCajaMonedaId,
        public readonly array $cards,
    ) {
    }
}