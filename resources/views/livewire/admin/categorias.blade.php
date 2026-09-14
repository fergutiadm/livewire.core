<div>
    <div class="container">
        @if($formularioVisible)
        <div class="panel-flotante panel-form"
            x-data="{ show: @entangle('formularioVisible') }"
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            x-cloak
        >
            {{-- Formulario --}}
            <form wire:submit.prevent="save">

                {{-- ============================================================
                    DATOS PRINCIPALES
                ============================================================= --}}
                <div class="grid grid-cols-1 md:grid-cols-6 gap-4">

                    {{-- Local --}}
                    <div class="md:col-span-1">
                        <div class="panel-form-group">
                            <x-label class="panel-form-label">Local</x-label>

                            <x-select
                                wire:model="localId"
                                wire:change="localChanged"
                                class="panel-form-input w-full"
                            >
                                @foreach($locales as $local)
                                    <option value="{{ $local->id }}">
                                        {{ $local->nombre }}
                                    </option>
                                @endforeach
                            </x-select>
                        </div>
                    </div>

                    {{-- Nombre --}}
                    <div class="md:col-span-2">
                        <div class="panel-form-group">
                            <x-label class="panel-form-label">Nombre</x-label>

                            <x-input
                                class="panel-form-input w-full"
                                wire:model="nombre"
                            />
                        </div>
                    </div>

                    {{-- Descripción --}}
                    <div class="md:col-span-2">
                        <div class="panel-form-group">
                            <x-label class="panel-form-label">Descripción</x-label>

                            <x-textarea
                                class="panel-form-input w-full"
                                wire:model="descripcion"
                            ></x-textarea>
                        </div>
                    </div>

                    {{-- % Descuento --}}
                    <div class="md:col-span-1">
                        <div class="panel-form-group">
                            <x-label class="panel-form-label">% Descuento</x-label>

                            <x-input
                                class="panel-form-input w-full"
                                wire:model="porciento_descuento"
                            />
                        </div>
                    </div>

                </div>


                {{-- ============================================================
                    ICONOS
                ============================================================= --}}
                <div class="grid grid-cols-1 md:grid-cols-6 gap-4 mt-6">

                    {{-- Icono principal --}}
                    <div class="md:col-span-3">
                        <div class="panel-form-group">

                            <x-label class="panel-form-label">
                                Icono Principal
                            </x-label>

                            <div class="grid grid-cols-6 gap-2 mt-2">

                                @foreach($iconGallery as $icon)

                                    <button
                                        type="button"
                                        wire:click="$set('icono', '{{ $icon }}')"
                                        class="
                                            p-2 rounded-lg border
                                            flex items-center justify-center
                                            {{ $icono === $icon
                                                ? 'border-indigo-500 ring-2 ring-indigo-500 bg-indigo-50 dark:bg-indigo-900/30'
                                                : 'border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800'
                                            }}
                                        "
                                    >
                                        <i class="bi {{ $icon }} text-lg"></i>
                                    </button>

                                @endforeach

                            </div>

                        </div>
                    </div>


                    {{-- Icono secundario --}}
                    <div class="md:col-span-3">
                        <div class="panel-form-group">

                            <x-label class="panel-form-label">
                                Icono Secundario
                            </x-label>

                            <div class="grid grid-cols-6 gap-2 mt-2">

                                @foreach($iconGallery as $icon)

                                    <button
                                        type="button"
                                        wire:click="$set('icono_secundario', '{{ $icon }}')"
                                        class="
                                            p-2 rounded-lg border
                                            flex items-center justify-center
                                            {{ $icono_secundario === $icon
                                                ? 'border-indigo-500 ring-2 ring-indigo-500 bg-indigo-50 dark:bg-indigo-900/30'
                                                : 'border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800'
                                            }}
                                        "
                                    >
                                        <i class="bi {{ $icon }} text-lg"></i>
                                    </button>

                                @endforeach

                            </div>

                        </div>
                    </div>


                    {{-- Preview de iconos --}}
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

                    {{-- Columna izquierda: imágenes --}}
                    <div>

                        {{-- Imagen minimalista --}}
                        <div class="panel-form-group">

                            <x-label class="panel-form-label">
                                Imagen Minimalista
                            </x-label>

                            <div class="flex items-center gap-3 mt-2">

                                <input
                                    type="file"
                                    wire:model="imagen_minimalista"
                                    class="hidden"
                                    id="minimalistaInput"
                                >

                                <button
                                    type="button"
                                    onclick="document.getElementById('minimalistaInput').click()"
                                    class="px-3 py-1 border rounded-lg bg-gray-700 hover:bg-gray-900 text-sm text-white"
                                >
                                    Seleccionar Imagen Minimalista
                                </button>

                                @if($imagen_minimalista)

                                    <img
                                        class="w-16 h-16 object-cover rounded-lg border"
                                        src="{{ $imagen_minimalista->temporaryUrl() }}"
                                    >

                                @endif

                            </div>

                        </div>


                        {{-- Imagen representativa --}}
                        <div class="panel-form-group mt-4">

                            <x-label class="panel-form-label">
                                Imagen Representativa
                            </x-label>

                            <div class="flex items-center gap-3 mt-2">

                                <input
                                    type="file"
                                    wire:model="imagen_representativa"
                                    class="hidden"
                                    id="representativaInput"
                                >

                                <button
                                    type="button"
                                    onclick="document.getElementById('representativaInput').click()"
                                    class="px-3 py-1 border rounded-lg bg-gray-700 hover:bg-gray-900 text-sm text-white"
                                >
                                    Seleccionar Imagen Representativa
                                </button>

                                @if($imagen_representativa)

                                    <img
                                        class="w-24 h-16 object-cover rounded-lg border"
                                        src="{{ $imagen_representativa->temporaryUrl() }}"
                                    >

                                @endif

                            </div>

                        </div>

                    </div>


                    {{-- Columna derecha: vista previa --}}
                    <div>

                        <div class="panel-form-group">

                            <x-label class="panel-form-label">
                                Vista Previa
                            </x-label>

                            <div class="max-w-sm mt-3 p-4 rounded-2xl shadow-md transition-all duration-200">

                                {{-- Iconos --}}
                                <div class="flex items-center gap-2 mb-3">

                                    <div class="text-3xl {{ $color_bg }} {{ $color_text }} px-2 py-0.5 rounded-full">

                                        @if($icono)
                                            <i class="bi {{ $icono }}"></i>
                                        @else
                                            📦
                                        @endif

                                    </div>

                                    @if($icono_secundario)

                                        <div class="text-3xl {{ $color_bg }} {{ $color_text }} px-2 py-0.5 rounded-full">

                                            <i class="bi {{ $icono_secundario }}"></i>

                                        </div>

                                    @endif

                                </div>


                                {{-- Nombre --}}
                                <div class="font-semibold text-lg">
                                    {{ $nombre ?: 'Nombre de Categoría' }}
                                </div>


                                {{-- Descripción --}}
                                <div class="text-sm opacity-80 mt-1">
                                    {{ $descripcion ?: 'Descripción de la categoría...' }}
                                </div>


                                {{-- Imagen minimalista --}}
                                @if($imagen_minimalista)

                                    <div class="mt-3">

                                        <img
                                            class="w-full h-24 object-cover rounded-lg"
                                            src="{{ $imagen_minimalista->temporaryUrl() }}"
                                        >

                                    </div>

                                @endif


                                {{-- Imagen representativa --}}
                                @if($imagen_representativa)

                                    <div class="mt-3">

                                        <img
                                            class="w-full h-24 object-cover rounded-lg"
                                            src="{{ $imagen_representativa->temporaryUrl() }}"
                                        >

                                    </div>

                                @endif


                                {{-- Badge --}}
                                <span
                                    class="inline-block mt-3 px-2 py-1 rounded"
                                    style="background-color: {{ $color_badge }}"
                                >
                                    Badge
                                </span>

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
                                        w-9 h-9 rounded-full border-2
                                        flex items-center justify-center
                                        {{ $p['bg'] }}
                                        {{
                                            $color_bg === $p['bg'] &&
                                            $color_text === $p['text']
                                                ? 'border-black ring-2 ring-offset-2 ring-black'
                                                : 'border-gray-300'
                                        }}
                                    "
                                >
                                    <span class="text-xs font-bold {{ $p['text'] }}">
                                        Aa
                                    </span>
                                </button>

                            @endforeach

                        </div>

                        @error('color_bg')
                            <p class="panel-form-error">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>

                </div>


                {{-- ============================================================
                    GALERÍA DE IMÁGENES
                ============================================================= --}}
                <div
                    class="mt-6"
                    x-data="{ mostrarUpload: @entangle('mostrarUpload') }"
                >

                    <div class="panel-form-group">

                        <x-label class="panel-form-label">
                            Galería de Imágenes
                        </x-label>


                        {{-- Toggle --}}
                        <button
                            type="button"
                            @click="mostrarUpload = !mostrarUpload"
                            class="
                                inline-flex items-center
                                px-3 py-1
                                border border-transparent
                                text-sm leading-4 font-medium
                                rounded-md text-white
                                transition-colors duration-200
                            "
                            :class="
                                mostrarUpload
                                    ? 'bg-red-500 hover:bg-red-600'
                                    : 'bg-indigo-600 hover:bg-indigo-700'
                            "
                        >

                            <span
                                x-text="mostrarUpload ? 'Ocultar Carga' : 'Mostrar Carga'"
                            ></span>

                            <svg
                                x-show="mostrarUpload"
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

                            <svg
                                x-show="!mostrarUpload"
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


                        {{-- Área de carga --}}
                        <div
                            x-show="mostrarUpload"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 transform scale-95"
                            x-transition:enter-end="opacity-100 transform scale-100"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 transform scale-100"
                            x-transition:leave-end="opacity-0 transform scale-95"

                            x-data="{
                                isDragging: false,
                                uploading: false,
                                progress: 0
                            }"

                            x-on:livewire-upload-start="uploading = true"
                            x-on:livewire-upload-finish="uploading = false; progress = 0"
                            x-on:livewire-upload-error="uploading = false"
                            x-on:livewire-upload-progress="progress = $event.detail.progress"

                            x-on:dragover.prevent="isDragging = true"
                            x-on:dragleave.prevent="isDragging = false"
                            x-on:drop.prevent="
                                isDragging = false;
                                $refs.fileInput.files = $event.dataTransfer.files
                            "

                            class="
                                mt-4
                                border-2 border-dashed
                                rounded-lg
                                p-8
                                mx-auto
                                transition-all
                                w-full
                                {{
                                    $errors->has('imagenes.*')
                                        ? 'border-red-400 bg-red-50'
                                        : 'bg-white border-gray-300'
                                }}
                            "

                            :class="
                                isDragging
                                    ? 'border-blue-500 bg-blue-50'
                                    : ''
                            "
                        >

                            {{-- Input --}}
                            <input
                                type="file"
                                wire:model="imagenes"
                                multiple
                                key="{{ $categoriaId ? 'edit-'.$categoriaId : 'nuevo' }}"
                                accept="image/jpeg,image/png,image/webp"
                                class="hidden"
                                x-ref="fileInput"
                            >


                            {{-- Contenido normal --}}
                            <div
                                x-show="!uploading"
                                class="flex flex-col items-center justify-center text-center"
                            >

                                <svg
                                    class="mx-auto h-12 w-12 text-gray-400"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 48 48"
                                    aria-hidden="true"
                                >
                                    <path
                                        d="M28 8H12a4 4 0 00-4 4v20m32-12v12c0 2.21-1.79 4-4 4H12c-2.21 0-4-1.79-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m-4-4l-1.172-1.172a4 4 0 01-5.656 0H8m28 0a4 4 0 100-8 4 4 0 000 8z"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />
                                </svg>


                                <p class="mt-2 text-base text-gray-600">
                                    <strong>Arrastra tus imágenes aquí</strong>
                                </p>

                                <p class="text-xs text-gray-400 mt-1 mb-4">
                                    o
                                </p>


                                <button
                                    type="button"
                                    @click.prevent="$refs.fileInput.click()"
                                    class="
                                        px-4 py-2
                                        border border-gray-300
                                        rounded-lg
                                        shadow-sm
                                        text-sm font-medium
                                        text-gray-700
                                        bg-white
                                        hover:bg-gray-50
                                        focus:outline-none
                                    "
                                >
                                    Elegir archivos
                                </button>


                                <p class="mt-2 text-xs text-gray-500">
                                    JPG, PNG, WEBP · Máx 5 MB c/u · Múltiples permitidas
                                </p>


                                <div
                                    class="
                                        mt-2
                                        inline-flex items-center
                                        px-3 py-0.5
                                        rounded-full
                                        text-sm font-medium
                                        bg-gray-100
                                        text-gray-800
                                    "
                                >
                                    {{ count($imagenes) }} seleccionada(s)
                                </div>

                            </div>


                            {{-- Barra de progreso --}}
                            <div
                                x-show="uploading"
                                x-cloak
                                class="py-2 text-center"
                            >

                                <div class="w-full bg-gray-200 rounded-full h-2">

                                    <div
                                        class="bg-blue-600 h-2 rounded-full transition-all duration-500 ease-out"
                                        :style="`width: ${progress}%`"
                                    ></div>

                                </div>

                                <span class="text-xs text-blue-600">
                                    Subiendo:
                                    <span x-text="progress"></span>%
                                </span>

                            </div>

                        </div>


                        {{-- Descripción --}}
                        <p
                            x-show="mostrarUpload"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 transform scale-95"
                            x-transition:enter-end="opacity-100 transform scale-100"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 transform scale-100"
                            x-transition:leave-end="opacity-0 transform scale-95"
                            class="mt-4 text-sm text-gray-600"
                        >
                            Puedes subir varias imágenes. Luego, debajo,
                            eliges la principal o eliminas existentes.
                        </p>


                        {{-- ====================================================
                            NUEVAS IMÁGENES
                        ===================================================== --}}
                        @if($imagenes)

                            <div class="mt-6">

                                <h4
                                    class="
                                        text-xs font-bold text-gray-400
                                        uppercase tracking-wider mb-2
                                    "
                                >
                                    Por subir (Temporales)
                                </h4>


                                <div class="flex gap-3 mt-2 flex-wrap">

                                    @foreach($imagenes as $index => $img)

                                        <div class="relative group h-24 w-24">

                                            <img
                                                src="{{ $img->temporaryUrl() }}"
                                                class="h-24 w-24 object-cover rounded-lg border shadow-sm"
                                            >


                                            <div
                                                class="
                                                    absolute inset-0
                                                    bg-black/40
                                                    opacity-0
                                                    group-hover:opacity-100
                                                    transition
                                                    rounded-lg
                                                    flex items-center justify-center gap-1
                                                "
                                            >

                                                {{-- Eliminar --}}
                                                <button
                                                    type="button"
                                                    wire:click="removeImage({{ $index }})"
                                                    class="
                                                        p-1
                                                        bg-red-600
                                                        text-white
                                                        rounded-full
                                                        hover:scale-110
                                                        transition
                                                    "
                                                >
                                                    <svg
                                                        class="w-4 h-4"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        viewBox="0 0 24 24"
                                                    >
                                                        <path
                                                            d="M6 18L18 6M6 6l12 12"
                                                        />
                                                    </svg>
                                                </button>


                                                {{-- Principal --}}
                                                <button
                                                    type="button"
                                                    wire:click="$set('primaryImageId', 'new-{{ $index }}')"
                                                    class="
                                                        p-1
                                                        bg-blue-600
                                                        text-white
                                                        rounded-full
                                                        hover:scale-110
                                                        transition
                                                    "
                                                >
                                                    <svg
                                                        class="w-4 h-4"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        viewBox="0 0 24 24"
                                                    >
                                                        <path
                                                            d="M5 13l4 4L19 7"
                                                        />
                                                    </svg>
                                                </button>

                                            </div>


                                            @if($primaryImageId == "new-{$index}")

                                                <span
                                                    class="
                                                        absolute -top-2 -right-2
                                                        bg-green-500
                                                        text-white
                                                        text-[10px]
                                                        px-2 py-0.5
                                                        rounded-full
                                                        shadow-sm
                                                        border border-white
                                                    "
                                                >
                                                    PRINCIPAL
                                                </span>

                                            @endif

                                        </div>

                                    @endforeach

                                </div>

                            </div>

                        @endif


                        {{-- ====================================================
                            IMÁGENES GUARDADAS
                        ===================================================== --}}
                        @if($medias && count($medias) > 0)

                            <div class="mt-6">

                                <h4
                                    class="
                                        text-xs font-bold text-gray-400
                                        uppercase tracking-wider mb-2
                                    "
                                >
                                    Galería Guardada
                                    (Arrastra para reordenar)
                                </h4>


                                <div
                                    x-data
                                    x-init="
                                        Sortable.create($el, {
                                            animation: 150,
                                            handle: '.drag-handle',
                                            onEnd: () => {
                                                $wire.reordenarMedias(
                                                    Array.from($el.children)
                                                        .map(el => el.dataset.id)
                                                )
                                            }
                                        })
                                    "
                                    class="flex gap-3 mt-2 flex-wrap"
                                >

                                    @foreach($medias as $media)

                                        <div
                                            data-id="{{ $media['id'] }}"
                                            class="
                                                relative group
                                                h-24 w-24
                                                cursor-move
                                                shadow-sm
                                                border
                                                rounded-lg
                                                overflow-hidden
                                            "
                                        >

                                            <img
                                                src="{{ asset($media['path']) }}"
                                                class="h-24 w-24 object-cover"
                                            >


                                            {{-- Drag handle --}}
                                            <div
                                                class="
                                                    drag-handle
                                                    absolute bottom-0 right-0
                                                    bg-black/60
                                                    text-white
                                                    p-1
                                                    rounded-tl-lg
                                                    opacity-0
                                                    group-hover:opacity-100
                                                    transition
                                                "
                                            >

                                                <svg
                                                    class="w-3 h-3"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M4 8h16M4 16h16"
                                                    />
                                                </svg>

                                            </div>

                                        </div>

                                    @endforeach

                                </div>

                            </div>

                        @endif


                        @error('imagenes.*')

                            <p class="panel-form-error mt-2">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>

                </div>


                {{-- ============================================================
                    BOTONES
                ============================================================= --}}
                <div class="panel-form-actions flex items-center gap-3 mt-6">

                    <x-button-save-cancel
                        modelId="{{ $categoriaId }}"
                        modelLabel="Categoría"
                    />

                </div>

            </form>


        </div>
        @endif

        <x-panel-toggle wire:loading.attr="disabled"
            :open="$formularioVisible"
            wire:model="formularioVisible"
        />

        {{-- Tabla de CategorÃ­as --}}
        <livewire:admin.tabla-categorias
            :local-id="$localId"
            :per-page="$perPage"
            key="tabla-categoria-{{ $localId }}"
            {{--  lazy  --}}
        />


    </div>

    {{-- Modal de eliminaciÃ³n --}}
    @if($categoriaIdToDelete)
        <x-confirmation-modal
        wire:model="confirmingCategoriaDeletion"
        onCancel="cancelDelete"
        onConfirm="delete"
        maxWidth="md"
    >
        {{-- TÃ­tulo personalizado para el slot title --}}
        <x-slot name="title">
            Confirmar eliminaciÃ³n
        </x-slot>

        {{-- Contenido del mensaje para el slot por defecto --}}
        <x-slot name="content">
            Â¿EstÃ¡ seguro de eliminar este categorÃ­a?
        </x-slot>

        <x-slot name="footer">
            <div class="flex justify-end gap-2">
                <button wire:click="cancelDelete" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancelar</button>
                <button wire:click="delete" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Eliminar</button>
            </div>
        </x-slot>
    </x-confirmation-modal>
    @endif

    {{-- Modal de Edicion de Atributos y sus Valores --}}
    @if($editAtributosShow)
    <div class="fixed inset-0 bg-gray-800 bg-opacity-75 z-50 flex items-center justify-center dark:text-slate-700">

        <div class="panel-layout bg-white rounded-lg shadow-lg w-full max-w-none lg:max-w-4xl">

            {{-- HEADER --}}
            <div class="panel-header m-2 item">
                <h3 class="text-lg font-semibold">
                    Editando Atributos â€“ Categoria {{ $categoriaEditAtributos->nombre }}
                </h3>
                <div class="panel-form-actions flex justify-end mt-4 gap-2">
                        <button
                            type="button"
                            class="btn-danger btn-icon"
                            wire:click="intentarCerrarModal"
                        >
                            Cancelar
                        </button>
                    </div>
            </div>

            {{-- PANEL IZQUIERDO --}}
            <div class="panel-left m-2 bg-white-1 overflow-x-auto">
                <div class="p-4">
                    <h4 class="font-semibold mb-3 flex items-center gap-2">
                        Atributos Seleccionados
                    </h4>

                    @livewire(
                        'select-atributo-cards-proxy',
                        ['modelType' => 'categoria', 'modelId' => $categoriaIdEditAtributos],
                        key('cards-'.$categoriaIdEditAtributos)
                    )
                </div>
            </div>

            {{-- PANEL DERECHO --}}
            <div class="panel-right m-2 panel-form">
                <form wire:submit.prevent="editAtributos">
                    @livewire(
                        'select-atributo-morph',
                        ['modelType' => 'categoria', 'modelId' => $categoriaIdEditAtributos]
                    )


                </form>
            </div>

        </div>
    </div>
    @endif



</div>

