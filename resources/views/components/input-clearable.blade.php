@props([
    'label' => '',
    'placeholder' => '',
])

<div class="space-y-1">
    @if($label)
        <label class="block text-sm font-medium text-slate-700">
            {{ $label }}
        </label>
    @endif

    <div 
        class="relative"
        x-data="{ 
            value: @entangle($attributes->wire('model')).live 
        }"
    >
        <input
            type="text"
            placeholder="{{ $placeholder }}"
            x-model="value"
            class="w-full rounded-xl border-slate-300 pr-10 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100"
            {{ $attributes->except('wire:model') }}
        />

        <!-- Botón de limpieza (Se muestra solo si hay texto escrito) -->
        <button
            type="button"
            x-show="value && value.length > 0"
            x-on:click="value = ''"
            x-cloak
            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-700 dark:text-gray-500 dark:hover:text-gray-200 transition-colors"
            title="Limpiar campo"
        >
            ✕
        </button>
    </div>
</div>
