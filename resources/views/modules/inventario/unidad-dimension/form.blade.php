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
        x-on:submit="$dispatch('loading-start', { message: 'Guardando unidades dimensión...' })"
        class="space-y-6"
    >

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

            {{-- CODIGO --}}
            <div>

                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Código
                </label>

                <input
                    type="text"
                    wire:model="codigo"
                    class="w-full rounded-xl border-slate-300"
                    placeholder="Código de la unidad dimensión"
                >
                @error('codigo')<p class="panel-form-error">{{ $message }}</p>@enderror
            </div>

            {{-- NOMBRE --}}
            <div>

                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Nombre
                </label>

                <input
                    type="text"
                    wire:model="nombre"
                    class="w-full rounded-xl border-slate-300"
                    placeholder="Nombre de la unidad dimensión"
                >
                @error('nombre')<p class="panel-form-error">{{ $message }}</p>@enderror
            </div>

            {{-- DESCRIPCION --}}
            <div>

                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Descripción
                </label>

                <textarea
                    wire:model="descripcion"
                    class="w-full rounded-xl border-slate-300"
                    placeholder="Descripción de la unidad dimensión"
                    rows="3"
                ></textarea>
                @error('descripcion')<p class="panel-form-error">{{ $message }}</p>@enderror
            </div>

            {{-- ACTIVO --}}
            <div class="flex flex-col items-center">

                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Activa
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
                        {{ $unidadDimensionId ? 'Guardar cambios' : 'Crear Unidad Dimensión' }}
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

    {{--  <div wire:key="unidad-dimension-media-manager-{{ $unidadDimensionId }}">
        <livewire:inv.unidad-dimension.media-manager
            :unidad-dimension-id="$unidadDimensionId"
        />
    </div>  --}}

    {{--  locunidad-dimensional-media-component.blade.php  --}}
</div>
