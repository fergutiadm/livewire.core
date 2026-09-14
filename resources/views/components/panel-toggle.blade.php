@props([
    'open' => false,
])

<div
    x-data="{ open: @entangle($attributes->wire('model')) }"
    class="panel-form-switch mb-4"
>
    <button
        type="button"
        @click="open = !open"
        class="
            group flex items-center gap-2
            text-gray-500 hover:text-gray-800
            transition-colors duration-200
            focus:outline-none
        "
        :title="open ? 'Ocultar listado' : 'Mostrar listado'"
    >
        <!-- Icono protagonista -->
        <i
    class="bi text-lg text-gray-400 group-hover:text-gray-700 transition"
    :class="open ? 'bi-chevron-up' : 'bi-chevron-down'"
></i>

        <!-- Texto tipo label -->
        <span class="hidden md:inline text-sm font-medium">
            <span x-show="!open" class="opacity-70 group-hover:opacity-100">
                Mostrar Formulario
            </span>
            <span x-show="open" class="opacity-70 group-hover:opacity-100">
                Ocultar Formulario
            </span>
        </span>
    </button>
</div>
