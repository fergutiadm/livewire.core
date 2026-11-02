<?php

namespace App\Http\Livewire\Currency\DTOs;

class SaveTasasCambioDTO
{
    public function __construct(
        public readonly array $monedas,
    ) {
    }
}