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
        x-on:submit="$dispatch('loading-start', { message: 'Guardando tarjeta magnética...' })"
        class="space-y-6"
    >

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

            {{-- MONEDA --}}
            <div>

                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Moneda
                </label>

                <select
                    wire:model="monedaId"
                    class="w-full rounded-xl border-slate-300"
                >

                    @foreach($monedas as $moneda)

                        <option value="{{ $moneda->id }}">
                            {{ $moneda->codigo }}
                        </option>

                    @endforeach

                </select>

                @error('monedaId')<p class="panel-form-error">{{ $message }}</p>@enderror

            </div>

            {{-- NUMERO --}}

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Número
                </label>
                <input
                    type="text"
                    x-data
                    x-on:input="
                        let numero = $event.target.value.replace(/\D/g, '').slice(0, 16);

                        let formateado = numero
                            .match(/.{1,4}/g)
                            ?.join('-') ?? '';

                        $event.target.value = formateado;

                        $wire.set('numero', formateado, false);
                    "
                    wire:model="numero"
                    class="w-full rounded-xl border-slate-300"
                    placeholder="Número de la tarjeta magnética"
                    maxlength="19"
                    inputmode="numeric"
                    autocomplete="off"
                >

                @error('numero')
                    <p class="panel-form-error">{{ $message }}</p>
                @enderror
            </div>



            {{-- NOMBRE --}}
            <div>

                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Nombre
                </label>

                <input
                    type="text"
                    wire:model="propietario"
                    class="w-full rounded-xl border-slate-300"
                    placeholder="Propietario de la tarjeta magnética"
                >
                @error('propietario')<p class="panel-form-error">{{ $message }}</p>@enderror
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

    {{--  <div wire:key="tarjeta-magnetica-media-manager-{{ $tarjetaMagneticaId }}">
        <livewire:inv.tarjeta-magnetica.media-manager
            :tarjeta-magnetica-id="$tarjetaMagneticaId"
        />
    </div>  --}}

    {{--  tarjeta-magnetica-media-component.blade.php  --}}
</div>
