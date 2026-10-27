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
                    ItemId
                </label>

                <input
                    type="text"
                    wire:model="item_id"
                    class="w-full rounded-xl border-slate-300"
                    placeholder="ItemId de la variable"
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

        
        {{-- ACTIONS --}}
        <div class="flex flex-wrap items-center gap-3">

            <button
                type="submit"
                wire:loading.attr="disabled"
                wire:target="save"
                class="
                    inline-flex items-center gap-2
                    rounded-xl bg-indigo-600
                    px-5 py-2 text-sm font-medium text-white
                    hover:bg-indigo-700
                    disabled:cursor-not-allowed disabled:opacity-60
                "
            >
                <span wire:loading.remove wire:target="save">
                    Guardar
                </span>

                <span wire:loading wire:target="save">
                    Guardando...
                </span>
            </button>

            <button
                type="button"
                wire:click="cancel"
                wire:loading.attr="disabled"
                wire:target="save"
                class="
                    inline-flex items-center
                    rounded-xl border border-slate-300
                    bg-white px-5 py-2
                    text-sm font-medium text-slate-700
                    hover:bg-slate-50
                    disabled:cursor-not-allowed disabled:opacity-60
                "
            >
                Cancelar
            </button>

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
