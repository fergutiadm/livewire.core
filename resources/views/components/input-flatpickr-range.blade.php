{{-- resources/views/components/input-flatpickr-range.blade.php --}}
@props([
    'label' => '',
    'placeholder' => 'dd-mm-aaaa',
    'minDate' => null,
    'maxDate' => null,
])

<div class="panel-form-group" wire:ignore>
    @if($label)
        <x-label class="panel-form-label">
            {{ $label }}
        </x-label>
    @endif

    <div
        class="relative"
        x-data="{
            date: @entangle($attributes->wire('model')).live,
            /* 
               CORRECCIÓN: Mapeamos los props directamente usando las llaves de Blade. 
               Si vienen nulos, Alpine los inicializará como cadenas vacías o null sin romper el servidor.
            */
            min: '{{ $minDate }}',
            max: '{{ $maxDate }}',
            fp: null
        }"
        x-init="
            fp = flatpickr($refs.input, {
                altInput: true,
                altFormat: 'd-m-Y',
                dateFormat: 'Y-m-d',
                defaultDate: date || null,
                allowInput: true,
                minDate: min || null,
                maxDate: max || null,

                onChange: function(selectedDates, dateStr) {
                    date = dateStr;
                },
                onClear: function() {
                    date = '';
                },
                onClose: function(selectedDates, dateStr) {
                    if (!dateStr) { date = ''; }
                }
            });

            // 🔄 Sincronización del valor de la fecha seleccionada
            $watch('date', value => {
                if (!fp) return;
                if (fp.input.value !== (value || '')) {
                    fp.setDate(value || null, false);
                }
            });

            // 🔒 Reactividad para cambios de Periodo Contable (Actualiza los límites al vuelo)
            $watch('min', value => { if (fp) fp.set('minDate', value || null); });
            $watch('max', value => { if (fp) fp.set('maxDate', value || null); });
        "
        {{--
          Si usas Livewire 3, puedes habilitar este listener reactivo en el div padre 
          para actualizar las variables de Alpine al vuelo si el componente se refresca:
        --}}
        x-effect="min = '{{ $minDate }}'; max = '{{ $maxDate }}';"
    >
        <input
            type="text"
            placeholder="{{ $placeholder }}"
            x-ref="input"
            class="panel-form-input border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 pr-10 form-select-responsive"
            {{ $attributes->filter(
                fn ($value, $key) => !str_starts_with($key, 'wire:model')
            ) }}
        />

        <button
            type="button"
            x-show="date"
            x-on:click="fp.clear(); date = '';"
            x-cloak
            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-700 dark:text-gray-500 dark:hover:text-gray-200"
            title="Limpiar fecha"
        >
            ✕
        </button>
    </div>
</div>
