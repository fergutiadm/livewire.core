@props([
    'variant' => 'default',
    'click' => null,
    'disabled' => 0
])

@php
    $variants = [
        'edit' => [
            'icon' => 'bi bi-pencil-fill',
            'class' => 'btn-primary',
            'tooltip' => 'Editar',
        ],

        'delete' => [
            'icon' => 'bi bi-trash-fill',
            'class' => 'btn-danger',
            'tooltip' => 'Eliminar',
        ],

        'atributos' => [
            'icon' => 'bi bi-sliders2-vertical',
            'class' => 'btn-secondary',
            'tooltip' => 'Editar atributos',
        ],
        'view' => [
            'icon' => 'bi bi-eye-fill',
            'class' => 'btn-secondary',
            'tooltip' => 'Ver',
        ],
        'default' => [
            'icon' => 'bi bi-question-circle',
            'class' => 'btn-icon',
            'tooltip' => '',
        ],
    ];

    $config = $variants[$variant] ?? $variants['default'];
@endphp

<button {{ $attributes->merge(['class' => $config['class'] . ' relative btn-icon disabled:opacity-25']) }}
        @if($click) wire:click="{{ $click }}" @endif
        x-data="{ show: false }"
        @mouseenter="show = true"
        @mouseleave="show = false"
        @if($disabled)
        @disabled($disabled)
        @endif
>
    @if(trim($slot))
        {{ $slot }}
    @else
        <i class="{{ $config['icon'] }}"></i>
    @endif

    @if($config['tooltip'])
        <div
            x-show="show"
            x-transition
            class="absolute bottom-full mb-2 left-1/2 -translate-x-1/2
                   bg-gray-900 text-white text-xs rounded px-2 py-1
                   whitespace-nowrap z-50"
        >
            {{ $config['tooltip'] }}
        </div>
    @endif
</button>
