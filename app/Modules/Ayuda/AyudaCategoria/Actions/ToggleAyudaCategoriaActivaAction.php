<?php

namespace App\Modules\Ayuda\AyudaCategoria\Actions;

use App\Models\AyudaCategoria;

class ToggleAyudaCategoriaActivaAction
{
    public function execute(AyudaCategoria $ayudaCategoria): AyudaCategoria
    {
        $ayudaCategoria->activa = ! $ayudaCategoria->activa;
        $ayudaCategoria->save();

        return $ayudaCategoria->fresh();
    }
}
