@props([
    'label' => '',
    'model' => null,
    'placeholder' => 'dd-mm-aaaa',
])

<div class="panel-form-group relative" x-data="{
        date: @entangle($attributes->wire('model')),
        show: false,
        toggle() { this.show = !this.show },
        setDate(d) { this.date = d; this.show = false },
        clear() { this.date = null }
    }">

    @if($label)
        <x-label class="panel-form-label">{{ $label }}</x-label>
    @endif

    <input type="text"
           x-model="date"
           @focus="show = true"
           placeholder="{{ $placeholder }}"
           class="panel-form-input pr-10 cursor-pointer"
           readonly>

    {{-- Icono calendario --}}
    <i class="bi bi-calendar-event absolute right-8 top-1/2 -translate-y-1/2 text-gray-400 cursor-pointer"
       @click="toggle()"></i>

    {{-- Botón limpiar --}}
    <button
        type="button"
        x-show="date"
        @click="clear()"
        class="absolute right-2 top-1/2 -translate-y-1/2 flex items-center text-slate-400 hover:text-red-500 transition"
        title="Vaciar fecha"
    >
        <i class="bi bi-eraser-fill"></i>
    </button>

    {{-- Datepicker básico --}}
    <div x-show="show" x-cloak class="absolute z-50 mt-1 bg-white border rounded shadow p-2">
        <input type="date" x-model="date" class="panel-form-input" @input="setDate($event.target.value)">
    </div>

</div>
