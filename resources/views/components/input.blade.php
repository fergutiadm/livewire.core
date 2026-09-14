@props(['disabled' => false, 'type' => 'text'])

<input
    type="{{ $type }}"
    {{ $disabled ? 'disabled' : '' }}
    {{ $attributes->merge([
        'class' => '
            border-gray-300
            focus:border-indigo-500
            focus:ring-indigo-500
            rounded-md shadow-sm

            dark:border-slate-600
            dark:bg-slate-800
            dark:text-slate-100
        '
    ]) }}
>
