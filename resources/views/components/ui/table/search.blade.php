<div class="flex items-center">

    <input
        type="text"
        {{ $attributes->merge([
            'class' => '
                w-full
                rounded-xl
                border
                border-gray-300
                bg-white
                px-4
                py-2
                text-sm
                shadow-sm
                focus:border-indigo-500
                focus:ring
                focus:ring-indigo-200
                focus:ring-opacity-50
            '
        ]) }}
    />

</div>