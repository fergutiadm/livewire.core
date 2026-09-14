<th
    {{ $attributes->merge([
        'class' => '
            px-4
            py-3
            text-left
            text-xs
            font-semibold
            uppercase
            tracking-wider
            text-gray-600
            whitespace-nowrap
            select-none
        '
    ]) }}
>

    {{ $slot }}

</th>