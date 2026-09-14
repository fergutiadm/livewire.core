<?php

namespace App\Observers;

use App\Models\Categoria;

class CategoriaObserver
{
    /**
     * Se ejecuta ANTES de guardar en la BD
     */
    public function creating(Categoria $categoria): void
    {
        // Si ya viene definido (ej: seeders), no tocar
        if (!is_null($categoria->orden_visual)) {
            return;
        }

        // Obtener el mayor orden del mismo local (o global)
        $maxOrden = Categoria::where('local_id', $categoria->local_id)
            ->max('orden_visual');

        $categoria->orden_visual = ($maxOrden ?? 0) + 1;
    }

    /**
     * Handle the Categoria "created" event.
     */
    public function created(Categoria $categoria): void
    {
        //
    }

    /**
     * Handle the Categoria "updated" event.
     */
    public function updated(Categoria $categoria): void
    {
        //
    }

    /**
     * Handle the Categoria "deleted" event.
     */
    public function deleted(Categoria $categoria): void
    {
        //
    }

    /**
     * Handle the Categoria "restored" event.
     */
    public function restored(Categoria $categoria): void
    {
        //
    }

    /**
     * Handle the Categoria "force deleted" event.
     */
    public function forceDeleted(Categoria $categoria): void
    {
        //
    }
}
