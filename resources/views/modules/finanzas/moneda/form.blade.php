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
        x-on:submit="$dispatch('loading-start', { message: 'Guardando moneda...' })"
        class="space-y-6"
    >

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

            {{-- CODIGO --}}
            <div>

                <label class="block text-sm font-medium text-slate-700 mb-1" data>
                    Código
                </label>

                <input
                    type="text"
                    wire:model="codigo"
                    class="w-full rounded-xl border-slate-300"
                    placeholder="Codigo de la moneda"
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
                    placeholder="Nombre de la moneda"
                >
                @error('nombre')<p class="panel-form-error">{{ $message }}</p>@enderror
            </div>

            {{-- SIMBOLO --}}
            <div>

                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Símbolo
                </label>

                <input
                    type="text"
                    wire:model="simbolo"
                    class="w-full rounded-xl border-slate-300"
                    placeholder="Simbolo de la moneda"
                >
                @error('simbolo')<p class="panel-form-error">{{ $message }}</p>@enderror
            </div>


        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

            {{-- TASA DE CAMBIO --}}
            <div>

                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Tasa de cambio
                </label>

                <input
                    type="text"
                    wire:model="tasa_cambio"
                    class="w-full rounded-xl border-slate-300"
                    placeholder="Tasa de cambio"
                >
                @error('tasa_cambio')<p class="panel-form-error">{{ $message }}</p>@enderror
            </div>

            {{-- Es Principal --}}
            <div class="flex flex-col items-center">

                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Principal
                </label>

                <input
                    type="checkbox"
                    wire:model="es_principal"
                    class="w-10 h-10 rounded-md border-slate-300"
                >
                @error('es_principal')<p class="panel-form-error">{{ $message }}</p>@enderror
            </div>

            {{-- ACTIVA --}}
            <div class="flex flex-col items-center">

                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Activa
                </label>

                <input
                    type="checkbox"
                    wire:model="activa"
                    class="w-10 h-10 rounded-md border-slate-300"
                >
                @error('activa')<p class="panel-form-error">{{ $message }}</p>@enderror
            </div>


        </div>

        {{-- ============================================================
            PALETA DE COLORES
        ============================================================= --}}

        <div class="mt-6">

            <div class="panel-form-group">

                <x-label class="panel-form-label">
                    Paleta de Color
                </x-label>

                <div class="flex flex-wrap gap-2 mt-2">

                    @foreach($palette as $p)

                        <button
                            type="button"
                            wire:click="selectPalette('{{ $p['bg'] }}', '{{ $p['text'] }}')"
                            class="
                                w-9
                                h-9
                                rounded-full
                                border-2
                                flex
                                items-center
                                justify-center
                                transition
                                {{ $p['bg'] }}

                                {{
                                    $color_bg === $p['bg'] &&
                                    $color_text === $p['text']
                                        ? 'border-black ring-2 ring-offset-2 ring-black'
                                        : 'border-gray-300'
                                }}
                            "
                            title="{{ $p['bg'] }} / {{ $p['text'] }}"
                        >
                            <span
                                class="
                                    text-xs
                                    font-bold
                                    {{ $p['text'] }}
                                "
                            >
                                Aa
                            </span>
                        </button>

                    @endforeach

                </div>

                @error('color_bg')
                    <p class="panel-form-error mt-2">
                        {{ $message }}
                    </p>
                @enderror

                @error('color_text')
                    <p class="panel-form-error mt-2">
                        {{ $message }}
                    </p>
                @enderror

            </div>

        </div>

        {{-- ACTIONS --}}
        <div class="flex items-center gap-3">

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

        </div>

    </form>

    {{-- ============================================================
        MEDIA MANAGER
    ============================================================= --}}

    {{--  <div wire:key="moneda-media-manager-{{ $monedaId }}">
        <livewire:finanzas.moneda.media-manager
            :moneda-id="$monedaId"
        />
    </div>  --}}

    {{--  moneda-media-component.blade.php  --}}
</div>
