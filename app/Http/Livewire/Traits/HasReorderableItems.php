<?php

namespace App\Http\Livewire\Traits;

use Illuminate\Support\Facades\DB;

trait HasReorderableItems
{
    /**
     * Reordenar cualquier conjunto de items persistente evitando colisiones UNIQUE.
     *
     * @param array $orderedIds
     * @param string $modelClass
     * @param callable|null $scopeQuery
     */
    protected function reorderItems(
        array $orderedIds,
        string $modelClass,
        ?callable $scopeQuery = null
    ): void {

        /*
        |--------------------------------------------------------------------------
        | Normalizar estructura de Livewire Sortable
        |--------------------------------------------------------------------------
        */
        $orderedIds = collect($orderedIds)
            ->map(function ($item) {

                if (is_array($item)) {
                    return $item['value']
                        ?? $item['id']
                        ?? null;
                }

                return $item;
            })
            ->filter()
            ->values()
            ->toArray();



        DB::transaction(function () use ($orderedIds, $modelClass, $scopeQuery) {

            /*
            |--------------------------------------------------------------------------
            | Query base con scope opcional
            |--------------------------------------------------------------------------
            */
            $query = $modelClass::query();

            if ($scopeQuery) {
                $query = $scopeQuery($query);
            }

            $items = $query->whereIn('id', $orderedIds)->get();

            /*
            |--------------------------------------------------------------------------
            | STEP 1 - mover a orden temporal negativo
            | evita violar UNIQUE(local_id, orden_visual)
            |--------------------------------------------------------------------------
            */
            foreach ($items as $item) {
                $item->update([
                    'orden_visual' => -$item->id
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | STEP 2 - asignar orden definitivo
            |--------------------------------------------------------------------------
            */
            foreach ($orderedIds as $index => $id) {

                $query = $modelClass::query();

                if ($scopeQuery) {
                    $query = $scopeQuery($query);
                }

                $query->where('id', $id)->update([
                    'orden_visual' => $index + 1
                ]);
            }
        });
    }
}
