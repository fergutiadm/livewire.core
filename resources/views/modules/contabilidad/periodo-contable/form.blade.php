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
        x-on:submit="$dispatch('loading-start', { message: 'Guardando periodo contable...' })"
        class="space-y-6"
    >

    <div>

        {{-- LOCAL --}}
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">
                Local
            </label>

            <select
                wire:model="localId"
                wire:change="localChanged"
                class="w-full rounded-xl border-slate-300"
            >
                <option value="">Seleccione un local</option>

                @foreach($locales as $local)
                    <option value="{{ $local->id }}">
                        {{ $local->nombre }}
                    </option>
                @endforeach
            </select>

            @error('localId')
                <p class="text-xs text-red-500 mt-1">
                    {{ $message }}
                </p>
            @enderror
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

            {{-- NOMBRE --}}
            <div>

                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Nombre
                </label>

                <input
                    type="text"
                    wire:model="nombre"
                    class="w-full rounded-xl border-slate-300"
                    placeholder="Nombre del local"
                >
                @error('nombre')<p class="panel-form-error">{{ $message }}</p>@enderror
            </div>

            {{-- ACTIVO --}}
            <div class="flex flex-col justify-center items-center">

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

            {{-- CERRADO --}}
            <div class="flex flex-col justify-center items-center">

                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Cerrado
                </label>

                <input
                    type="checkbox"
                    wire:model="cerrado"
                    class="w-10 h-10 rounded-md border-slate-300"
                >
                @error('cerrado')<p class="panel-form-error">{{ $message }}</p>@enderror
            </div>

        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

            {{-- Fecha inicio --}}
            <x-input-flatpickr
                wire:key="fecha-inicio-{{ $periodoId ?? 'new' }}"
                label="Fecha Inicio"
                wire:model.defer="fecha_inicio"
                placeholder="dd-mm-aaaa"
            />

            {{-- Fecha fin --}}
            <x-input-flatpickr
                wire:key="fecha-fin-{{ $periodoId ?? 'new' }}"
                label="Fecha Fin"
                wire:model.defer="fecha_fin"
                placeholder="dd-mm-aaaa"
            />

        </div>

        {{-- ACTIONS --}}
        {{-- <div class="flex items-center gap-3">

            <button
                type="submit"
                class="
                    inline-flex
                    items-center
                    rounded-xl
                    bg-indigo-600
                    px-5
                    py-2
                    text-sm
                    font-medium
                    text-white
                    hover:bg-indigo-700
                "
            >

                Guardar

            </button>

            <button
                type="button"
                wire:click="cancel"
                class="
                    inline-flex
                    items-center
                    rounded-xl
                    border
                    border-slate-300
                    bg-white
                    px-5
                    py-2
                    text-sm
                    font-medium
                    text-slate-700
                    hover:bg-slate-50
                "
            >

                Cancelar

            </button>

        </div> --}}
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
                        {{ $periodoContableId ? 'Guardar cambios' : 'Crear periodo' }}
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

    {{--  <div wire:key="local-media-manager-{{ $localId }}">
        <livewire:inv.local.media-manager
            :local-id="$localId"
        />
    </div>  --}}

    {{--  local-media-component.blade.php  --}}
</div>
