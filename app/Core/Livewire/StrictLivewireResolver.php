<?php

namespace App\Core\Livewire;

use InvalidArgumentException;

class StrictLivewireResolver
{
    public static function resolve(string $name): string
    {
        /**
         * 🚨 1. BLOQUEAR FQCN (App\Modules\...)
         */
        if (str_contains($name, '\\')) {
            throw new InvalidArgumentException(
                "❌ FQCN detected in Livewire call: {$name}. Use registered alias instead."
            );
        }

        /**
         * 🚨 2. BLOQUEAR "app." prefix (tu error actual)
         */
        if (str_starts_with($name, 'app.')) {
            throw new InvalidArgumentException(
                "❌ Invalid Livewire alias [{$name}]. Do NOT use FQCN transformed names."
            );
        }

        /**
         * 🚨 3. VALIDAR REGISTRY
         */
        if (!LivewireRegistry::exists($name)) {
            throw new InvalidArgumentException(
                "❌ Livewire component not registered: {$name}"
            );
        }

        return LivewireRegistry::get($name);
    }
}