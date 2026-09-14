@props([
    'formularioVisible' => true,
])

<div x-data="{ value: @entangle($attributes->wire('model')).defer }">
    <button
        @click="formularioVisible = !formularioVisible "
        type="button" class="btn-primary-1 btn-icon" title="Editar">
        <i class="bi bi-airplane-engines-fill"></i>
    </button>
    {{ $formularioVisible }}
</div>
