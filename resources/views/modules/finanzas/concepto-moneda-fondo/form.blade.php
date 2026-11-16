{{-- resources/views/modules/finanzas/concepto-moneda-fondo/form.blade.php --}}
<div
    class="
        rounded-2xl
        border
        border-slate-200
        bg-white
        p-6
        shadow-sm
    "
>

    <form novalidate
        wire:submit.prevent="save"
        x-on:submit="$dispatch('loading-start', { message: 'Guardando concepto...' })"
        class="space-y-6"
    >

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

            {{-- NOMBRE --}}
            <div>

                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Nombre
                </label>

                <input
                    type="text"
                    wire:model="nombre"
                    maxlength="150"
                    class="w-full rounded-xl border-slate-300"
                    placeholder="Nombre del concepto"
                >
                @error('nombre')<p class="panel-form-error">{{ $message }}</p>@enderror
            </div>

            {{-- TIPO --}}
            <div>

                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Tipo
                </label>

                <select
                    wire:model="tipo"
                    {{-- wire:change="tipoChanged" --}}
                    wire:loading.attr="disabled"
                    {{-- wire:target="tipoChanged" --}}
                    class="w-full rounded-xl border-slate-300"
                >
                    @foreach($tipos as $tipo)
                        <option value="{{ $tipo['id'] }}">
                            {{ $tipo['nombre'] }}
                        </option>
                    @endforeach
                </select>
                @error('tipo')<p class="panel-form-error">{{ $message }}</p>@enderror
            </div>

            {{-- ENTRADA-SALIDA --}}
            <div class="flex flex-col items-center">

                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Entrada/Salida
                </label>

                <input
                    type="checkbox"
                    wire:model="entrada_salida"
                    class="w-10 h-10 rounded-md border-slate-300"
                >
                @error('entrada_salida')<p class="panel-form-error">{{ $message }}</p>@enderror
            </div>

            {{-- ACTIVO --}}
            <div class="flex flex-col items-center">

                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Activo
                </label>

                <input
                    type="checkbox"
                    wire:model="activo"
                    class="w-10 h-10 rounded-md border-slate-300"
                >
                @error('activo')<p class="panel-form-error">{{ $message }}</p>@enderror
            </div>

        </div>

        {{-- ============================================================
        PIE DEL FORMULARIO
        ============================================================ --}}
        <div class="flex flex-col gap-4 border-t border-slate-200 pt-5 sm:flex-row justify-end">

            {{-- <div class="text-xs text-slate-500">
                Los datos se guardarán al confirmar.
            </div> --}}

            <div class="flex flex-wrap justify-end items-center gap-3">

                {{-- CANCELAR --}}
                <button
                    type="button"
                    wire:click="cancel"
                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-300"
                >
                    <i class="bi bi-x-lg"></i>
                    Cancelar
                </button>

                {{-- GUARDAR --}}
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    wire:target="save"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60"
                >

                    <span wire:loading.remove wire:target="save">
                        <i class="bi bi-floppy"></i>
                        {{ $conceptoMonedaFondoId ? 'Guardar cambios' : 'Crear Concepto' }}
                    </span>

                    <span wire:loading wire:target="save">
                        <i class="bi bi-arrow-repeat"></i>
                        Guardando...
                    </span>

                </button>

            </div>

        </div>

    </form>

    {{-- ============================================================
        MEDIA MANAGER
    ============================================================= --}}

    {{--  <div wire:key="concepto-moneda-fondo-media-manager-{{ $conceptoMonedaFondoId }}">
        <livewire:finanzas.concepto-moneda-fondo.media-manager
            :concepto-moneda-fondo-id="$conceptoMonedaFondoId"
        />
    </div>  --}}

    {{--  concepto-moneda-fondo-media-component.blade.php  --}}
</div>
