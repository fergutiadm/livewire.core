<?php

namespace App\Modules\Comercial\Gestor\Actions;

use App\Models\Gestor;

class ToggleGestorActivoAction
{
    public function execute(Gestor $gestor): Gestor
    {
        $gestor->activo = ! $gestor->activo;

        $gestor->save();

        return $gestor->fresh();
    }
}