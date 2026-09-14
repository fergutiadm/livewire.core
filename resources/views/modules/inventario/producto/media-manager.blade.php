<div>

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


            {{-- ====================================================
                 TOGGLE
            ===================================================== --}}

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
                    x-text="
                        mostrarUpload
                            ? 'Ocultar Carga'
                            : 'Mostrar Carga'
                    "
                ></span>

                {{-- Flecha arriba --}}
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

                {{-- Flecha abajo --}}
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


            {{-- ====================================================
                 ÁREA DE CARGA
            ===================================================== --}}

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

                x-on:livewire-upload-start="
                    uploading = true
                "

                x-on:livewire-upload-finish="
                    uploading = false;
                    progress = 0
                "

                x-on:livewire-upload-error="
                    uploading = false
                "

                x-on:livewire-upload-progress="
                    progress = $event.detail.progress
                "

                x-on:dragover.prevent="
                    isDragging = true
                "

                x-on:dragleave.prevent="
                    isDragging = false
                "

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

                    {{ $errors->has('imagenes.*')
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

                {{-- =================================================
                     INPUT
                ================================================== --}}

                <input
                    type="file"
                    wire:model="imagenes"
                    multiple
                    wire:key="producto-media-input-{{ $productoId ?? 'nuevo' }}"
                    accept="image/jpeg,image/png,image/webp"
                    class="hidden"
                    x-ref="fileInput"
                >


                {{-- =================================================
                     CONTENIDO NORMAL
                ================================================== --}}

                <div
                    x-show="!uploading"
                    class="
                        flex
                        flex-col
                        items-center
                        justify-center
                        text-center
                    "
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


                    {{-- Elegir archivos --}}

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


                    {{-- Cantidad seleccionada --}}

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
                        {{ count($imagenes ?? []) }} seleccionada(s)
                    </div>

                </div>


                {{-- =================================================
                     BARRA DE PROGRESO
                ================================================== --}}

                <div
                    x-show="uploading"
                    x-cloak
                    class="py-2 text-center"
                >

                    <div class="w-full bg-gray-200 rounded-full h-2">

                        <div
                            class="
                                bg-blue-600
                                h-2
                                rounded-full
                                transition-all
                                duration-500
                                ease-out
                            "
                            :style="`width: ${progress}%`"
                        ></div>

                    </div>

                    <span class="text-xs text-blue-600">
                        Subiendo:
                        <span x-text="progress"></span>%
                    </span>

                </div>

            </div>


            {{-- ====================================================
                 DESCRIPCIÓN
            ===================================================== --}}

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
                Puedes subir varias imágenes. Luego,
                debajo, eliges la principal o eliminas existentes.
            </p>


            {{-- ====================================================
                 NUEVAS IMÁGENES
            ===================================================== --}}

            @if(!empty($imagenes))

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

                            <div
                                wire:key="producto-temporary-image-{{ $index }}"
                                class="relative group h-24 w-24"
                            >

                                <img
                                    src="{{ $img->temporaryUrl() }}"
                                    class="
                                        h-24 w-24
                                        object-cover
                                        rounded-lg
                                        border
                                        shadow-sm
                                    "
                                >


                                {{-- Controles --}}

                                <div
                                    class="
                                        absolute inset-0
                                        bg-black/40
                                        opacity-0
                                        group-hover:opacity-100
                                        transition
                                        rounded-lg
                                        flex items-center
                                        justify-center
                                        gap-1
                                    "
                                >

                                    {{-- Eliminar temporal --}}

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


                                {{-- Indicador de principal --}}

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

            @if(!empty($medias))

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
                        class="
                            flex
                            gap-3
                            mt-2
                            flex-wrap
                        "
                    >

                        @foreach($medias as $media)

                            <div
                                wire:key="media-{{ $media['id'] }}"
                                data-id="{{ $media['id'] }}"
                                class="
                                    relative
                                    group
                                    h-24
                                    w-24
                                    cursor-move
                                    shadow-sm
                                    border
                                    rounded-lg
                                    overflow-hidden
                                "
                            >

                                <img
                                    src="{{ asset($media['path']) }}"
                                    class="
                                        h-24
                                        w-24
                                        object-cover
                                    "
                                >


                                {{-- Drag handle --}}

                                <div
                                    class="
                                        drag-handle
                                        absolute
                                        bottom-0
                                        right-0
                                        z-20    
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


                                {{-- Controles de Media guardada --}}

                                <div
                                    class="
                                        absolute
                                        inset-0
                                        bg-black/40
                                        opacity-0
                                        group-hover:opacity-100
                                        transition
                                        rounded-lg
                                        flex
                                        items-center
                                        justify-center
                                        gap-1
                                    "
                                >

                                    {{-- Marcar principal --}}

                                    @if(!$media['is_primary'])

                                        <button
                                            type="button"
                                            wire:click="marcarPrimariaProducto({{ $media['id'] }})"
                                            class="
                                                p-1
                                                bg-blue-600
                                                text-white
                                                rounded-full
                                                hover:scale-110
                                                transition
                                            "
                                            title="Marcar como principal"
                                        >
                                            <svg
                                                class="w-4 h-4"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M5 13l4 4L19 7"
                                                />
                                            </svg>
                                        </button>

                                    @endif


                                    {{-- Marcar para eliminar --}}

                                    <button
                                        type="button"
                                        wire:click="marcarParaEliminar({{ $media['id'] }})"
                                        class="
                                            p-1
                                            bg-red-600
                                            text-white
                                            rounded-full
                                            hover:scale-110
                                            transition
                                        "
                                        title="Eliminar imagen"
                                    >
                                        <svg
                                            class="w-4 h-4"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"
                                            />
                                        </svg>
                                    </button>

                                </div>


                                {{-- Indicador de principal --}}

                                @if($media['is_primary'])

                                    <span
                                        class="
                                            absolute
                                            -top-2
                                            -right-2
                                            bg-green-500
                                            text-white
                                            text-[10px]
                                            px-2
                                            py-0.5
                                            rounded-full
                                            shadow-sm
                                            border
                                            border-white
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
                 ERRORES
            ===================================================== --}}

            @error('imagenes.*')

                <p class="panel-form-error mt-2">
                    {{ $message }}
                </p>

            @enderror

        </div>

    </div>

</div>
