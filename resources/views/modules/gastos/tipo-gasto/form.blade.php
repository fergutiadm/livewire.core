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
        x-on:submit="$dispatch('loading-start', { message: 'Guardando tipo de gasto...' })"
        class="space-y-6"
    >

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

            {{-- LOCAL --}}
            <div class="col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Local
                </label>

               <select
                    wire:model="localId"
                    wire:change="localChanged"
                    wire:loading.attr="disabled"
                    wire:target="localChanged"
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

            {{-- FIJO --}}
            <div class="flex flex-col items-center">

                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Fijo
                </label>

                <input
                    type="checkbox"
                    wire:model="fijo"
                    class="w-10 h-10 rounded-md border-slate-300"
                >
                @error('fijo')<p class="panel-form-error">{{ $message }}</p>@enderror
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

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            {{-- NOMBRE --}}
            <div>

                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Nombre
                </label>

                <input
                    type="text"
                    wire:model="nombre"
                    class="w-full rounded-xl border-slate-300" placeholder="Nombre del tipo de gasto"
                >
                @error('nombre')<p class="panel-form-error">{{ $message }}</p>@enderror
            </div>

            {{-- DESCRIPCIÓN --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Descripción
                </label>

                <textarea
                    wire:model="descripcion"
                    class="w-full rounded-xl border-slate-300"
                    placeholder="Descripción del tipo de gasto"
                    rows="3"
                ></textarea>

                @error('descripcion')
                    <p class="panel-form-error">{{ $message }}</p>
                @enderror
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
</div>
