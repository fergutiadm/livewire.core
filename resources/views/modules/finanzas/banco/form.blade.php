{{-- resources/views/modules/finanzas/banco/form.blade.php --}}
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
        x-on:submit="$dispatch('loading-start', { message: 'Guardando banco...' })"
        class="space-y-6"
    >

        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">

            {{-- PROVINCIA --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Provincia
                </label>

                <select
                    wire:model.live="provinciaId"
                    wire:loading.attr="disabled"
                    wire:target="provinciaId"
                    class="form-select-responsive"
                >
                    <option value="">Seleccione</option>

                    @foreach($provincias as $provincia)
                        <option value="{{ $provincia->id }}">
                            {{ $provincia->nombre }}
                        </option>
                    @endforeach
                </select>

                @error('provinciaId')
                    <p class="text-xs text-red-500 mt-1">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- MUNICIPIO --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Municipio
                </label>

                <select
                    wire:key="banco-municipio-{{ $provinciaId }}"
                    wire:model.change="municipio_id"
                    wire:loading.attr="disabled"
                    wire:target="provinciaId"
                    class="form-select-responsive"
                >
                    <option value="">
                        {{ $provinciaId ? 'Seleccione...' : 'Seleccione primero la provincia...' }}
                    </option>

                    @foreach($municipios as $municipio)
                        <option value="{{ $municipio['id'] }}">
                            {{ $municipio['nombre'] }}
                        </option>
                    @endforeach
                </select>

                @error('municipio_id')
                    <p class="text-xs text-red-500 mt-1">
                        {{ $message }}
                    </p>
                @enderror
            </div>

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
                    placeholder="Nombre del banco"
                >
                @error('nombre')<p class="panel-form-error">{{ $message }}</p>@enderror
            </div>

            {{-- ALIAS --}}
            <div>

                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Alias
                </label>

                <input
                    type="text"
                    wire:model="alias"
                    maxlength="150"
                    class="w-full rounded-xl border-slate-300"
                    placeholder="Alias del banco"
                >
                @error('alias')<p class="panel-form-error">{{ $message }}</p>@enderror
            </div>

            {{-- CODIGO_SWITCH --}}
            <div>

                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Código SWIFT
                </label>

                <input
                    type="text"
                    wire:model="codigo_swift"
                    maxlength="150"
                    class="w-full rounded-xl border-slate-300"
                    placeholder="Código SWIFT del banco"
                >
                @error('codigo_swift')<p class="panel-form-error">{{ $message }}</p>@enderror
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

            {{-- DIRECCION --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Dirección
                </label>

                <input
                    type="text"
                    wire:model="direccion"
                    maxlength="255"
                    class="w-full rounded-xl border-slate-300"
                    placeholder="Dirección del banco"
                >

                @error('direccion')
                    <p class="panel-form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- TELEFONOS --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Teléfonos
                </label>

                <input
                    type="text"
                    wire:model="telefonos"
                    maxlength="255"
                    class="w-full rounded-xl border-slate-300"
                    placeholder="Teléfonos del banco"
                >

                @error('telefonos')
                    <p class="panel-form-error">{{ $message }}</p>
                @enderror
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

                {{-- USAR_FILTROS --}}
                <div>
                    <div class="flex flex-row items-center">

                        <label class="block text-sm font-medium text-slate-700 my-1 px-2 text-nowrap">
                            Filtrar Resultado
                        </label>

                        <input
                            type="checkbox"
                            wire:model="usar_filtros"
                            wire:change="aplicarFiltrosResultado()"
                            class="w-5 h-5 rounded-md border-slate-300"
                        >
                    </div>
                </div>

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
                        {{ $bancoId ? 'Guardar cambios' : 'Crear Banco' }}
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

    {{--  <div wire:key="banco-media-manager-{{ $bancoId }}">
        <livewire:finanzas.banco.media-manager
            :banco-id="$bancooId"
        />
    </div>  --}}

    {{--  banco-media-component.blade.php  --}}
</div>
