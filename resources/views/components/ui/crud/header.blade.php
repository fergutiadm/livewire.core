@props([
    'title',
    'description' => null,
    'showForm' => true,
    'showToggle' => true,
    'toggleMethod' => 'toggleForm',
    'showText' => 'Mostrar formulario',
    'hideText' => 'Ocultar formulario',
    'selectorPeriodo' => false,
    'selectorFechaOperacion' => false,
    'gestionCierre' => false,
])

<!-- Contenedor adaptativo: flex-col en móviles, sm:flex-row en pantallas más grandes -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-100 pb-4">

    <!-- Bloque de Título -->
    <div class="flex-1">
        <h1 class="text-xl font-bold text-slate-800 md:text-2xl">
            {{ $title }}
        </h1>
        @if($description)
            <p class="text-xs text-slate-500 md:text-sm mt-0.5">
                {{ $description }}
            </p>
        @endif
    </div>

    <!-- Contenedor de Acciones (Selectores y Botón) -->
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full sm:w-auto">
        
        @if($selectorPeriodo)
            <div class="w-full sm:w-auto">
                <livewire:navigation.periodo-selector />
            </div>
            @if($selectorFechaOperacion)
                <div class="w-full sm:w-auto">
                    <livewire:navigation.fecha-operacion-selector />
                </div>
            @endif
        @endif

        @if($gestionCierre)
            <div class="w-full sm:w-auto">
                <a href="{{ route('cierres_caja') }}"
                    class="flex w-full items-center justify-between gap-2 px-3 py-2 text-xs md:text-sm font-medium text-slate-700 shadow-sm hover:bg-sky-50 transition-colors"
                >
                    <div class="flex items-center gap-1.5 min-w-0">
                        <span class="text-sky-600/80 font-normal">Gestionar cierre diario</span>
                    </div>
                    <svg class="h-4 w-4 flex-shrink-0 text-sky-600/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"/>
                    </svg>
                </a>
            </div>
        @endif

        @if($showToggle)
            <button
                type="button"
                wire:click="{{ $toggleMethod }}"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-800 px-4 py-2 text-xs md:text-sm font-medium text-white hover:bg-slate-700 transition w-full sm:w-auto shadow-sm"
            >
                <!-- Icono dinámico sugerido para mejorar la interfaz en móviles -->
                @if($showForm)
                    <svg class="w-4 4-4 text-slate-300" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>
                @else
                    <svg class="w-4 h-4 text-slate-300" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                @endif
                <span>{{ $showForm ? $hideText : $showText }}</span>
            </button>
        @endif

    </div>
</div>
