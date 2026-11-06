{{-- resources/views/modules/configuracion/setup-config/form.blade.php --}}
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
        x-on:submit="$dispatch('loading-start', { message: 'Guardando setup-config...' })"
        class="space-y-6"
    >

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

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

            {{-- NOMBRE --}}
        <div>

            <label class="block text-sm font-medium text-slate-700 mb-1">
                Nombre
            </label>

            <input
                type="text"
                wire:model="nombre"
                class="w-full rounded-xl border-slate-300"
                placeholder="Nombre de la variable"
            >
            @error('nombre')<p class="panel-form-error">{{ $message }}</p>@enderror
        </div>

            {{-- ITEM_ID --}}
            <div>

                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Identificador
                </label>

                <input
                    type="text"
                    wire:model="item_id"
                    class="w-full rounded-xl border-slate-300"
                    placeholder="Identificador de la variable"
                >
                @error('item_id')<p class="panel-form-error">{{ $message }}</p>@enderror
            </div>

            {{-- DESCRIPCION --}}
            <div>

                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Descripción
                </label>

                <textarea
                    wire:model="descripcion"
                    class="w-full rounded-xl border-slate-300"
                    placeholder="Descripción de la variable"
                    rows="3"
                ></textarea>
                @error('descripcion')<p class="panel-form-error">{{ $message }}</p>@enderror
            </div>

        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            {{-- VALOR --}}
            <div>

                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Valor
                </label>

                <textarea
                    wire:model="valor"
                    class="w-full rounded-xl border-slate-300"
                    placeholder="Valor de la variable"
                    rows="3"
                ></textarea>
                @error('valor')<p class="panel-form-error">{{ $message }}</p>@enderror
            </div>        

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- VALOR_INICIAL --}}
                <div>

                    <label class="block text-sm font-medium text-slate-700 mb-1">
                        Valor Inicial
                    </label>

                    <input
                        type="text"
                        wire:model="valor_inicial"
                        class="w-full rounded-xl border-slate-300"
                        placeholder="Valor Inicial de la variable"
                    >
                    @error('valor_inicial')<p class="panel-form-error">{{ $message }}</p>@enderror
                </div>

                {{-- VALOR_MINIMO --}}
                <div>

                    <label class="block text-sm font-medium text-slate-700 mb-1">
                        Valor Minimo
                    </label>

                    <input
                        type="text"
                        wire:model="valor_minimo"
                        class="w-full rounded-xl border-slate-300"
                        placeholder="Valor Minimo de la variable"
                    >
                    @error('valor_minimo')<p class="panel-form-error">{{ $message }}</p>@enderror
                </div>

                {{-- VALOR_MAXIMO --}}
                <div>

                    <label class="block text-sm font-medium text-slate-700 mb-1">
                        Valor Maximo
                    </label>

                    <input
                        type="text"
                        wire:model="valor_maximo"
                        class="w-full rounded-xl border-slate-300"
                        placeholder="Valor Maximo de la variable"
                    >
                    @error('valor_maximo')<p class="panel-form-error">{{ $message }}</p>@enderror
                </div>

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
                        {{ $setupConfigId ? 'Guardar cambios' : 'Crear variable' }}
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

    {{--  <div wire:key="setup-config-media-manager-{{ $setupConfigId }}">
        <livewire:inv.setup-config.media-manager
            :setup-config-id="$setupConfigId"
        />
    </div>  --}}

    {{--  setup-config-media-component.blade.php  --}}
</div>
