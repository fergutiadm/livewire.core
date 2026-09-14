@props([
    'placeholder' => '',
    'type' => 'text',
    'style' => null,
])

<div
    class="relative w-full"
    x-data="{ value: @entangle($attributes->wire('model')).defer }"
>
    <input
        type="{{ $type }}"
        x-model="value"
        placeholder="{{ $placeholder }}"
        {{ $attributes->merge(['class' => '
                                            panel-form-input pr-10

                                            dark:border-slate-600
                                            dark:bg-slate-800
                                            dark:text-slate-100
                                          ']) }}
    />

    <button
        type="button"
        x-show="value"
        x-transition
        @click="value = null"
        class="
                absolute inset-y-0
                right-2 flex
                items-center
                text-slate-400
                hover:text-red-500

                dark:text-slate-100
                dark:hover:text-red-500
              "
        title="Vaciar"
    >
        <i class="bi bi-eraser-fill"></i>
    </button>
</div>
