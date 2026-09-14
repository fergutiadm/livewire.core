@props([
    'modelId' => 0,
    'modelLabel' => '',
    'wire_target' => 'save'
])
<div>
    <x-button
        type="submit"
        class="bg-indigo-600 hover:bg-indigo-700"
        wire:loading.attr="disabled"
    >
    <!-- Spinner: Solo visible mientras se ejecuta 'save' -->
    <svg wire:loading wire:target="save" class="animate-spin h-4 w-4 text-white mr-2" xmlns="http://www.w3.org" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
    </svg>

    <!-- Texto Dinámico -->
    <span wire:loading.remove wire:target="save">
        {{ $modelId ? 'Actualizar ' . $modelLabel : 'Crear ' . $modelLabel }}
    </span>
    <span wire:loading wire:target="save">
        {{ $modelId ? 'Actualizando...' : 'Creando...' }}
    </span>
</x-button>

<!-- Botón Cancelar / Limpiar -->
<button
    type="button"
    wire:click="cancel"
    wire:loading.attr="disabled"
    class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition duration-150 ease-in-out disabled:opacity-50"
>
    {{ $modelId ? 'Cancelar' : 'Limpiar' }}
</button>
</div>
