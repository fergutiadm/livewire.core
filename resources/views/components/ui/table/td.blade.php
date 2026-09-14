<td
    {{ $attributes->merge([
        'class' => '
            px-4
            py-3
            text-sm
            text-gray-700
            whitespace-nowrap
            align-middle
        '
    ]) }}
>

    {{ $slot }}

</td>