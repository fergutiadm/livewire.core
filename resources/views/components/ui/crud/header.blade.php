@props([
    'title',
    'description' => null,
    'showForm' => true,
    'showToggle' => true,
    'toggleMethod' => 'toggleForm',
    'showText' => 'Mostrar formulario',
    'hideText' => 'Ocultar formulario',
])

<div class="flex items-center justify-between">

    <div>
        <h1 class="text-2xl font-bold text-slate-800">
            {{ $title }}
        </h1>

        @if($description)
            <p class="text-sm text-slate-500">
                {{ $description }}
            </p>
        @endif
    </div>

    @if($showToggle)
        <button
            type="button"
            wire:click="{{ $toggleMethod }}"
            class="
                inline-flex
                items-center
                gap-2
                rounded-xl
                bg-slate-800
                px-4
                py-2
                text-sm
                font-medium
                text-white
                hover:bg-slate-700
            "
        >
            {{ $showForm ? $hideText : $showText }}
        </button>
    @endif

</div>