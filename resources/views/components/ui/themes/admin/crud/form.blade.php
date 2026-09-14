@props([
    'show' => true,
])

<div
    @class([
        'transition-all duration-200',
        'hidden' => ! $show,
    ])
>
    {{ $slot }}
</div>