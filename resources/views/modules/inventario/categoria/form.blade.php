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
        x-on:submit="$dispatch('loading-start', { message: 'Guardando categoría...' })"
        class="space-y-6"
    >


    {{-- ============================================================
        DATOS BÁSICOS
    ============================================================= --}}

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

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
                placeholder="Nombre de la categoría"
            >

            @error('nombre')
                <p class="panel-form-error">{{ $message }}</p>
            @enderror
        </div>

        {{-- DESCRIPCIÓN --}}
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">
                Descripción
            </label>

            <textarea
                wire:model="descripcion"
                class="w-full rounded-xl border-slate-300"
                placeholder="Descripción de la categoría"
                rows="3"
            ></textarea>

            @error('descripcion')
                <p class="panel-form-error">{{ $message }}</p>
            @enderror
        </div>


        {{-- % DESCUENTO --}}
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">
                % Descuento
            </label>

            <input
                type="number"
                step="0.01"
                min="0"
                wire:model="porciento_descuento"
                class="w-full rounded-xl border-slate-300"
                placeholder="Porciento de descuento"
            >

            @error('porciento_descuento')
                <p class="panel-form-error">{{ $message }}</p>
            @enderror
        </div>

    </div>

    {{-- ============================================================
        CONFIGURACIÓN VISUAL
    ============================================================= --}}

    <div
    x-data="{ mostrarConfiguracionVisual: false }"
    >

    {{-- ========================================================
        TOGGLE
    ========================================================= --}}

    <div class="mb-4">
        <button
            type="button"
            @click="mostrarConfiguracionVisual = !mostrarConfiguracionVisual"
            class="
                inline-flex items-center
                px-3 py-1
                border border-transparent
                text-sm leading-4 font-medium
                rounded-md text-white
                transition-colors duration-200
            "
             :class="
                mostrarConfiguracionVisual
                    ? 'bg-red-500 hover:bg-red-600'
                    : 'bg-indigo-600 hover:bg-indigo-700'
            "
        >
            <span
                x-text="
                    mostrarConfiguracionVisual
                        ? 'Ocultar Configuración Visual'
                        : 'Mostrar Configuración Visual'
                "
            ></span>

            {{-- Flecha arriba --}}
            <svg
                x-show="mostrarConfiguracionVisual"
                class="ml-2 h-4 w-4"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M5 15l7-7 7 7"
                />
            </svg>

            {{-- Flecha abajo --}}
            <svg
                x-show="!mostrarConfiguracionVisual"
                class="ml-2 h-4 w-4"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M19 9l-7 7-7-7"
                />
            </svg>
        </button>
    </div>

    {{-- TODO LO VISUAL --}}
    <div
        x-show="mostrarConfiguracionVisual"
        x-transition
    >
        {{-- ============================================================
            ICONOS
        ============================================================= --}}

        <div class="grid grid-cols-1 md:grid-cols-6 gap-4 mt-6">

            {{-- ICONO PRINCIPAL --}}
            <div class="md:col-span-3">
                <div class="panel-form-group">

                    <x-label class="panel-form-label">
                        Icono Principal
                    </x-label>

                    <div class="grid grid-cols-6 gap-2 mt-2">

                        @foreach($iconGallery as $icon)
                            <button
                                type="button"
                                wire:click="seleccionarIcono('{{ $icon }}')"
                                class="
                                    p-2
                                    rounded-lg
                                    border
                                    flex
                                    items-center
                                    justify-center
                                    transition
                                    {{ $icono === $icon
                                        ? 'border-indigo-500 ring-2 ring-indigo-500 bg-indigo-50 dark:bg-indigo-900/30'
                                        : 'border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800'
                                    }}
                                "
                                title="{{ $icon }}"
                            >
                                <i class="bi {{ $icon }} text-lg"></i>
                            </button>
                        @endforeach

                    </div>

                    @error('icono')
                        <p class="panel-form-error mt-2">
                            {{ $message }}
                        </p>
                    @enderror

                </div>
            </div>


            {{-- ICONO SECUNDARIO --}}
            <div class="md:col-span-3">
                <div class="panel-form-group">

                    <x-label class="panel-form-label">
                        Icono Secundario
                    </x-label>

                    <div class="grid grid-cols-6 gap-2 mt-2">

                        @foreach($iconGallery as $icon)
                            <button
                                type="button"
                                wire:click="seleccionarIconoSecundario('{{ $icon }}')"
                                class="
                                    p-2
                                    rounded-lg
                                    border
                                    flex
                                    items-center
                                    justify-center
                                    transition
                                    {{ $icono_secundario === $icon
                                        ? 'border-indigo-500 ring-2 ring-indigo-500 bg-indigo-50 dark:bg-indigo-900/30'
                                        : 'border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800'
                                    }}
                                "
                                title="{{ $icon }}"
                            >
                                <i class="bi {{ $icon }} text-lg"></i>
                            </button>
                        @endforeach

                    </div>

                    @error('icono_secundario')
                        <p class="panel-form-error mt-2">
                            {{ $message }}
                        </p>
                    @enderror

                </div>
            </div>


            {{-- PREVIEW ICONOS --}}
            <div class="md:col-span-6">

                <div class="panel-form-group">

                    <x-label class="panel-form-label">
                        Vista previa de iconos
                    </x-label>

                    <div class="flex items-center gap-3 mt-2">

                        @if($icono)
                            <div class="text-3xl">
                                <i class="bi {{ $icono }}"></i>
                            </div>
                        @endif

                        @if($icono_secundario)
                            <div class="text-3xl">
                                <i class="bi {{ $icono_secundario }}"></i>
                            </div>
                        @endif

                    </div>

                </div>

            </div>

        </div>


        {{-- ============================================================
            IMÁGENES + VISTA PREVIA
        ============================================================= --}}

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">

            {{-- ========================================================
                COLUMNA IZQUIERDA
            ========================================================= --}}

            <div>

                {{-- IMAGEN MINIMALISTA --}}
                <div class="panel-form-group">

                    <x-label
                        for="categoria-minimalista-input"
                        class="panel-form-label"
                    >
                        Imagen Minimalista
                    </x-label>

                    <div class="flex items-center gap-3 mt-2">

                        <input
                            type="file"
                            wire:model="imagen_minimalista"
                            class="hidden"
                            id="categoria-minimalista-input"
                            accept="image/*"
                        >

                        <button
                            type="button"
                            onclick="document.getElementById('categoria-minimalista-input').click()"
                            class="
                                px-3
                                py-1
                                border
                                rounded-lg
                                bg-gray-700
                                hover:bg-gray-900
                                text-sm
                                text-white
                            "
                        >
                            Seleccionar Imagen Minimalista
                        </button>

                        @if($imagen_minimalista)
                            <img
                                class="w-16 h-16 object-cover rounded-lg border"
                                src="{{ is_string($imagen_minimalista)
                                    ? asset('storage/' . $imagen_minimalista)
                                    : $imagen_minimalista->temporaryUrl() }}"
                                alt="Vista previa minimalista"
                            >
                        @endif

                    </div>

                    @error('imagen_minimalista')
                        <p class="text-xs text-red-500 mt-1">
                            {{ $message }}
                        </p>
                    @enderror

                    <div
                        wire:loading
                        wire:target="imagen_minimalista"
                        class="text-xs text-slate-500 mt-2"
                    >
                        Cargando imagen...
                    </div>

                </div>


                {{-- IMAGEN REPRESENTATIVA --}}
                <div class="panel-form-group mt-4">

                    <x-label
                        for="categoria-representativa-input"
                        class="panel-form-label"
                    >
                        Imagen Representativa
                    </x-label>

                    <div class="flex items-center gap-3 mt-2">

                        <input
                            type="file"
                            wire:model="imagen_representativa"
                            class="hidden"
                            id="categoria-representativa-input"
                            accept="image/*"
                        >

                        <button
                            type="button"
                            onclick="document.getElementById('categoria-representativa-input').click()"
                            class="
                                px-3
                                py-1
                                border
                                rounded-lg
                                bg-gray-700
                                hover:bg-gray-900
                                text-sm
                                text-white
                            "
                        >
                            Seleccionar Imagen Representativa
                        </button>

                        @if($imagen_representativa)
                            <img
                                class="w-24 h-16 object-cover rounded-lg border"
                                src="{{ is_string($imagen_representativa)
                                    ? asset('storage/' . $imagen_representativa)
                                    : $imagen_representativa->temporaryUrl() }}"
                                alt="Vista previa representativa"
                            >
                        @endif

                    </div>

                    @error('imagen_representativa')
                        <p class="text-xs text-red-500 mt-1">
                            {{ $message }}
                        </p>
                    @enderror

                    <div
                        wire:loading
                        wire:target="imagen_representativa"
                        class="text-xs text-slate-500 mt-2"
                    >
                        Cargando imagen...
                    </div>

                </div>

            </div>


            {{-- ========================================================
                COLUMNA DERECHA — VISTA PREVIA
            ========================================================= --}}

            <div>

                <div class="panel-form-group">

                    <x-label class="panel-form-label">
                        Vista Previa
                    </x-label>

                    <div
                        class="
                            max-w-sm
                            mt-3
                            p-4
                            rounded-2xl
                            shadow-md
                            transition-all
                            duration-200
                            {{ $color_bg }}
                            {{ $color_text }}
                        "
                    >

                        {{-- ICONOS --}}
                        <div class="flex items-center gap-2 mb-3">

                            @if($icono)

                                <div
                                    class="
                                        text-3xl
                                        {{ $color_bg }}
                                        {{ $color_text }}
                                        px-2
                                        py-0.5
                                        rounded-full
                                    "
                                >
                                    <i class="bi {{ $icono }}"></i>
                                </div>

                            @else

                                <div
                                    class="
                                        text-3xl
                                        {{ $color_bg }}
                                        {{ $color_text }}
                                        px-2
                                        py-0.5
                                        rounded-full
                                    "
                                >
                                    📦
                                </div>

                            @endif


                            @if($icono_secundario)

                                <div
                                    class="
                                        text-3xl
                                        {{ $color_bg }}
                                        {{ $color_text }}
                                        px-2
                                        py-0.5
                                        rounded-full
                                    "
                                >
                                    <i class="bi {{ $icono_secundario }}"></i>
                                </div>

                            @endif

                        </div>


                        {{-- NOMBRE --}}
                        <div class="font-semibold text-lg">
                            {{ $nombre ?: 'Nombre de Categoría' }}
                        </div>


                        {{-- DESCRIPCIÓN --}}
                        <div class="text-sm opacity-80 mt-1">
                            {{ $descripcion ?: 'Descripción de la categoría...' }}
                        </div>


                        {{-- IMAGEN MINIMALISTA --}}
                        @if($imagen_minimalista)

                            <div class="mt-3">

                                <img
                                    class="
                                        w-full
                                        h-24
                                        object-cover
                                        rounded-lg
                                    "
                                    src="{{ is_string($imagen_minimalista)
                                        ? asset('storage/' . $imagen_minimalista)
                                        : $imagen_minimalista->temporaryUrl() }}"
                                    alt="Imagen minimalista"
                                >

                            </div>

                        @endif


                        {{-- IMAGEN REPRESENTATIVA --}}
                        @if($imagen_representativa)

                            <div class="mt-3">

                                <img
                                    class="
                                        w-full
                                        h-24
                                        object-cover
                                        rounded-lg
                                    "
                                    src="{{ is_string($imagen_representativa)
                                        ? asset('storage/' . $imagen_representativa)
                                        : $imagen_representativa->temporaryUrl() }}"
                                    alt="Imagen representativa"
                                >

                            </div>

                        @endif


                        {{-- BADGE --}}
                        @if($color_badge)

                            <span
                                class="
                                    inline-block
                                    mt-3
                                    px-2
                                    py-1
                                    rounded
                                    text-xs
                                    font-medium
                                "
                                style="background-color: {{ $color_badge }}"
                            >
                                Badge
                            </span>

                        @endif

                    </div>

                </div>

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
    </div>


    {{-- ============================================================
        ESPACIO RESERVADO PARA FUTUROS CAMPOS
    ============================================================= --}}

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

        {{-- COSTO --}}

    </div>


    {{-- ============================================================
        ACTIONS
    ============================================================= --}}

    <div class="flex items-center gap-3 pt-2">

        {{-- GUARDAR --}}
        <button
            type="submit"
            wire:loading.attr="disabled"
            wire:target="save"
            class="
                inline-flex
                items-center
                gap-2
                rounded-xl
                bg-indigo-600
                px-5
                py-2
                text-sm
                font-medium
                text-white
                hover:bg-indigo-700
                disabled:opacity-50
                disabled:cursor-not-allowed
            "
        >

            <span
                wire:loading
                wire:target="save"
                class="
                    inline-block
                    w-4
                    h-4
                    border-2
                    border-white
                    border-t-transparent
                    rounded-full
                    animate-spin
                "
            ></span>

            <span>
                Guardar
            </span>

        </button>


        {{-- CANCELAR --}}
        <button
            type="button"
            wire:click="cancel"
            wire:loading.attr="disabled"
            wire:target="cancel"
            class="
                inline-flex
                items-center
                gap-2
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
                disabled:opacity-50
                disabled:cursor-not-allowed
            "
        >
            Cancelar
        </button>

    </div>

</form>

{{-- ============================================================
    MEDIA MANAGER
============================================================= --}}

<div wire:key="categoria-media-manager">
    <livewire:inventario.categoria.media-manager
        :categoria-id="$categoriaId"
    />
</div>

{{-- ============================================================
    ATTRIBUTES MANAGER
============================================================= --}}

<div wire:key="categoria-attributes-manager">
    <livewire:inventario.categoria.attributes-manager
        :categoria-id="$categoriaId"
    />
</div>

</div>
