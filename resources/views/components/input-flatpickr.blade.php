@props([
'label' => '',
'model' => null,
'placeholder' => 'dd-mm-aaaa',
])

<div
    class="panel-form-group"
    wire:ignore
>
    @if($label)
        <x-label class="panel-form-label">
            {{ $label }}
        </x-label>
    @endif


    <div
        class="relative"
        x-data="{
            date: @entangle($attributes->wire('model')).live,
            fp: null
        }"
        x-init="
            fp = flatpickr($refs.input, {
                altInput: true,
                altFormat: 'd-m-Y',
                dateFormat: 'Y-m-d',
                defaultDate: date || null,
                allowInput: true,

                onChange: function(selectedDates, dateStr) {
                    date = dateStr;
                },

                onClear: function() {
                    date = '';
                },

                onClose: function(selectedDates, dateStr) {
                    if (!dateStr) {
                        date = '';
                    }
                }
            });

            $watch('date', value => {
                if (!fp) {
                    return;
                }

                const currentValue = fp.input.value;

                if (currentValue !== (value || '')) {
                    fp.setDate(value || null, false);
                }
            });
        "
    >
    <input
        type="text"
        placeholder="{{ $placeholder }}"
        x-ref="input"
        class="
            panel-form-input
            border-gray-300
            focus:border-indigo-500
            focus:ring-indigo-500
            rounded-md shadow-sm
            dark:border-slate-600
            dark:bg-slate-800
            dark:text-slate-100
            pr-10
        "
        {{ $attributes->except('wire:model') }}
    />

    <button
        type="button"
        x-show="date"
        x-on:click="
            fp.clear();
            date = '';
        "
        x-cloak
        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-700 dark:text-gray-500 dark:hover:text-gray-200"
        title="Limpiar fecha"
    >
        ✕
    </button>
</div>


</div>
