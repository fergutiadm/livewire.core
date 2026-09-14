<?php

namespace App\Core\Forms\Traits;

use Livewire\Component;

trait ClearsValidationOnUpdate
{
    public function updated($property): void
    {
        /** @var Component $this */
        $this->resetValidation($property);
    }
}
