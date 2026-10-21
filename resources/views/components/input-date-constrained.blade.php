@props([
    'label' => '',
    'minDate' => null,
    'maxDate' => null,
])

<div class="panel-form-group">
    @if($label)
        <label class="block text-sm font-medium text-slate-700 mb-1">
            {{ $label }}
        </label>
    @endif

    <div class="relative w-full">
        <!-- 
          Input Nativo de Fecha:
          - min y max bloquean físicamente cualquier día fuera del periodo contable.
          - form-select-responsive aplica el tamaño ultra compacto de 9px en móviles.
        -->
        <input
            type="date"
            min="{{ $minDate }}"
            max="{{ $maxDate }}"
            class="form-select-responsive w-full bg-white border-slate-300 rounded-xl focus:border-indigo-500 focus:ring-indigo-500"
            {{ $attributes->except(['label', 'minDate', 'maxDate']) }}
        />
    </div>
</div>
