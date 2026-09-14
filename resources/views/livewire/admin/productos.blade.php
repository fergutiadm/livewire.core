@php
    $encabezados = ['Nombre', 'Categoría', 'Costo', 'Precio', '% Descuento'];
@endphp

<div>

    <div class="container">

        {{-- =========================================================
             FORMULARIO PRODUCTO
        ========================================================== --}}

        @if($formularioVisible)

            <div
                class="panel panel-form"
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

                <form wire:submit.prevent="save">

                    {{-- =====================================================
                         LOCAL / CATEGORÍA / NOMBRE
                    ====================================================== --}}

                    <div class="panel-form-grid-3">

                        <div
                            class="panel-form-group"
                            wire:key="container-local-{{ $localId }}"
                        >
                            <x-label class="panel-form-label">
                                Local
                            </x-label>

                            <x-select
                                wire:model.live="localId"
                                wire:change="localChanged"
                                wire:key="select-local-{{ $localId }}"
                                class="panel-form-input"
                            >
                                <option value="" disabled>
                                    Seleccione
                                </option>

                                @foreach($locales as $local)
                                    <option value="{{ $local->id }}">
                                        {{ $local->nombre }}
                                    </option>
                                @endforeach
                            </x-select>

                            @error('localId')
                                <p class="panel-form-error">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>


                        <div class="panel-form-group">

                            <x-label class="panel-form-label">
                                Categoría
                            </x-label>

                            <x-select
                                wire:model.live="categoriaId"
                                wire:change="categoriaChanged"
                                class="panel-form-input"
                            >
                                <option value="" disabled>
                                    Seleccione
                                </option>

                                @foreach($categorias as $cat)
                                    <option value="{{ $cat->id }}">
                                        {{ $cat->nombre }}
                                    </option>
                                @endforeach
                            </x-select>

                            @error('categoriaId')
                                <p class="panel-form-error">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>


                        <div class="panel-form-group">

                            <x-label class="panel-form-label">
                                Nombre
                            </x-label>

                            <x-input
                                class="panel-form-input"
                                wire:model="nombre"
                            />

                            @error('nombre')
                                <p class="panel-form-error">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>

                    </div>


                    {{-- =====================================================
                         MONEDA / COSTO / PRECIO / DESCUENTO
                    ====================================================== --}}

                    <div class="panel-form-grid-4">

                        <div class="panel-form-group">

                            <x-label class="panel-form-label">
                                Moneda
                            </x-label>

                            <x-select
                                wire:model="monedaId"
                                class="panel-form-input"
                            >
                                <option value="" disabled>
                                    Seleccione
                                </option>

                                @foreach($monedas as $moneda)
                                    <option value="{{ $moneda->id }}">
                                        {{ $moneda->codigo }} ({{ $moneda->simbolo }})
                                    </option>
                                @endforeach
                            </x-select>

                            @error('monedaId')
                                <p class="panel-form-error">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>


                        <div class="panel-form-group">

                            <x-label class="panel-form-label">
                                Costo
                            </x-label>

                            <x-input
                                type="number"
                                step="0.01"
                                class="panel-form-input"
                                wire:model="costo"
                            />

                            @error('costo')
                                <p class="panel-form-error">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>


                        <div class="panel-form-group">

                            <x-label class="panel-form-label">
                                Precio
                            </x-label>

                            <x-input
                                type="number"
                                step="0.01"
                                class="panel-form-input"
                                wire:model="precio"
                            />

                            @error('precio')
                                <p class="panel-form-error">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>


                        <div class="panel-form-group">

                            <x-label class="panel-form-label">
                                % Descuento
                            </x-label>

                            <x-input
                                type="number"
                                step="0.01"
                                class="panel-form-input"
                                wire:model="porciento_descuento"
                            />

                            @error('porciento_descuento')
                                <p class="panel-form-error">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>

                    </div>


                    {{-- =====================================================
                         CÓDIGO / BARCODE
                    ====================================================== --}}

                    <div class="panel-form-grid-4 mt-4">

                        {{-- Código editable --}}

                        <div class="panel-form-group">

                            <x-label class="panel-form-label">
                                Código
                            </x-label>

                            <x-input
                                class="panel-form-input"
                                wire:model="codigo"
                            />

                            @error('codigo')
                                <p class="panel-form-error">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>


                        {{-- Barcode preview --}}

                        <div class="panel-form-group">

                            <x-label class="panel-form-label">

                                Código generado

                                @if($codigo)

                                    <span
                                        wire:click="descargarBarcode"
                                        class="cursor-pointer inline-flex items-center gap-1 text-xs text-slate-50 whitespace-nowrap font-bold bg-black/60 px-2 rounded-full"
                                    >
                                        DESCARGAR
                                    </span>

                                @endif

                            </x-label>


                            <div class="panel-form-input bg-gray-100 flex items-center justify-center">

                                @if($codigo)

                                    <img
                                        src="data:image/png;base64,{{ $this->generarBarcode() }}"
                                        wire:click="descargarBarcode"
                                        class="h-16 cursor-pointer"
                                    >

                                @else

                                    Se generará al guardar

                                @endif

                            </div>

                        </div>


                        {{-- Botones de código --}}

                        @if($codigo)

                            <div class="panel-form-group flex flex-row gap-2 mt-2 items-end">

                                {{-- Imprimir --}}

                                <button
                                    type="button"
                                    class="btn-primary flex items-center justify-center gap-2 px-4 py-2 whitespace-nowrap"
                                    onclick="printBarcode()"
                                >

                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.5"
                                        stroke="currentColor"
                                        class="w-5 h-5"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M6 9V4h12v5M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5h-2M6 14h12v6H6v-6z"
                                        />
                                    </svg>

                                    Imprimir

                                </button>


                                {{-- Ver ZPL --}}

                                <button
                                    type="button"
                                    class="btn-secondary flex items-center justify-center gap-2 px-4 py-2 whitespace-nowrap"
                                    wire:click="verCodigoZpl"
                                >

                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.5"
                                        stroke="currentColor"
                                        class="w-5 h-5"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M20.25 10.5L12 18.75l-9-9V5.25A2.25 2.25 0 015.25 3h4.5l10.5 10.5z"
                                        />

                                        <circle
                                            cx="7.5"
                                            cy="7.5"
                                            r="1.25"
                                            fill="currentColor"
                                        />

                                    </svg>

                                    Ver ZPL

                                </button>

                            </div>

                        @endif

                    </div>


                    {{-- =====================================================
                         GALERÍA
                    ====================================================== --}}

                    <div
                        class="col-span-full"
                        x-data="{ mostrarUpload: @entangle('mostrarUpload') }"
                    >

                        <div class="panel-form-group">

                            <x-label class="panel-form-label">
                                Galería de Imágenes
                            </x-label>


                            {{-- Switch / Toggle --}}

                            <button
                                type="button"
                                @click="mostrarUpload = !mostrarUpload"
                                class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-white transition-colors duration-200"
                                :class="mostrarUpload
                                    ? 'bg-red-500 hover:bg-red-600'
                                    : 'bg-indigo-600 hover:bg-indigo-700'"
                            >

                                <span
                                    x-text="mostrarUpload
                                        ? 'Ocultar Carga'
                                        : 'Mostrar Carga'"
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
                                class="border-2 border-dashed rounded-lg p-8 mx-auto transition-all w-full {{ $errors->has('imagenes.*') ? 'border-red-400 bg-red-50' : 'bg-white border-gray-300' }}"
                                :class="isDragging
                                    ? 'border-blue-500 bg-blue-50'
                                    : ''"
                            >

                                {{-- Input real --}}

                                <input
                                    type="file"
                                    wire:model="imagenes"
                                    multiple
                                    key="{{ $productoId ? 'edit-'.$productoId : 'nuevo' }}"
                                    accept="image/jpeg,image/png,image/webp"
                                    class="hidden"
                                    x-ref="fileInput"
                                >


                                {{-- Contenido --}}

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
                                        class="px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none"
                                    >
                                        Elegir archivos
                                    </button>


                                    <p class="mt-2 text-xs text-gray-500">
                                        JPG, PNG, WEBP · Máx 5 MB c/u · Múltiples permitidas
                                    </p>


                                    <div class="mt-2 inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
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
                                Puedes subir varias imágenes. Luego, debajo, eliges la principal o eliminas existentes.
                            </p>


                            {{-- =================================================
                                 NUEVAS IMÁGENES
                            ================================================== --}}

                            @if($imagenes)

                                <div class="mt-6">

                                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">
                                        Por subir (Temporales)
                                    </h4>

                                    <div class="flex gap-3 mt-2 flex-wrap">

                                        @foreach($imagenes as $index => $img)

                                            <div class="relative group h-24 w-24">

                                                <img
                                                    src="{{ $img->temporaryUrl() }}"
                                                    class="h-24 w-24 object-cover rounded-lg border shadow-sm"
                                                >


                                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition rounded-lg flex items-center justify-center gap-1">

                                                    <button
                                                        type="button"
                                                        wire:click="removeImage({{ $index }})"
                                                        class="p-1 bg-red-600 text-white rounded-full hover:scale-110 transition"
                                                    >
                                                        <svg
                                                            class="w-4 h-4"
                                                            fill="none"
                                                            stroke="currentColor"
                                                            viewBox="0 0 24 24"
                                                        >
                                                            <path d="M6 18L18 6M6 6l12 12"/>
                                                        </svg>
                                                    </button>


                                                    <button
                                                        type="button"
                                                        wire:click="$set('primaryImageId', 'new-{{ $index }}')"
                                                        class="p-1 bg-blue-600 text-white rounded-full hover:scale-110 transition"
                                                    >
                                                        <svg
                                                            class="w-4 h-4"
                                                            fill="none"
                                                            stroke="currentColor"
                                                            viewBox="0 0 24 24"
                                                        >
                                                            <path d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                    </button>

                                                </div>


                                                @if($primaryImageId == "new-{$index}")

                                                    <span class="absolute -top-2 -right-2 bg-green-500 text-white text-[10px] px-2 py-0.5 rounded-full shadow-sm border border-white">
                                                        PRINCIPAL
                                                    </span>

                                                @endif

                                            </div>

                                        @endforeach

                                    </div>

                                </div>

                            @endif


                            {{-- =================================================
                                 IMÁGENES GUARDADAS
                            ================================================== --}}

                            @if($medias && count($medias) > 0)

                                <div class="mt-6">

                                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">
                                        Galería Guardada (Arrastra para reordenar)
                                    </h4>


                                    <div
                                        x-data
                                        x-init="
                                            Sortable.create($el, {
                                                animation: 150,
                                                handle: '.drag-handle',
                                                onEnd: () => {
                                                    $wire.reorderProductos(
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
                                                class="relative group h-24 w-24 cursor-move shadow-sm border rounded-lg overflow-hidden"
                                            >

                                                <img
                                                    src="{{ asset($media['path']) }}"
                                                    class="h-24 w-24 object-cover"
                                                >


                                                <div class="drag-handle absolute bottom-0 right-0 bg-black/60 text-white p-1 rounded-tl-lg opacity-0 group-hover:opacity-100 transition">

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


                    {{-- =====================================================
                         ACCIONES FORMULARIO
                    ====================================================== --}}

                    <div class="panel-form-actions">

                        <x-button
                            type="submit"
                            class="btn-primary"
                            wire:loading.attr="disabled"
                        >

                            <svg
                                wire:loading
                                wire:target="save"
                                class="animate-spin h-4 w-4 text-white mr-2"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                            >
                                <circle
                                    class="opacity-25"
                                    cx="12"
                                    cy="12"
                                    r="10"
                                    stroke="currentColor"
                                    stroke-width="4"
                                />

                                <path
                                    class="opacity-75"
                                    fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                />
                            </svg>


                            <span wire:loading.remove wire:target="save">
                                {{ $productoId ? 'Salvar' : 'Crear' }}
                            </span>


                            <span wire:loading wire:target="save">
                                {{ $productoId ? 'Salvando...' : 'Creando...' }}
                            </span>

                        </x-button>


                        <button
                            type="button"
                            wire:click="cancel"
                            wire:loading.attr="disabled"
                            class="btn-secondary disabled:opacity-50"
                        >
                            Cancelar
                        </button>

                    </div>

                </form>

            </div>


            {{-- =========================================================
                 ÁREA DE IMPRESIÓN
            ========================================================== --}}

            @if($codigo)

                <div id="print-area" class="hidden">

                    <div style="text-align:center;">

                        <h3>
                            {{ $nombre }}
                        </h3>

                        <img
                            src="data:image/png;base64,{{ $this->generarBarcode() }}"
                        >

                        <p>
                            {{ $codigo }}
                        </p>

                    </div>

                </div>

            @endif

        @endif


        {{-- =========================================================
             TOGGLE FORMULARIO
        ========================================================== --}}

        <x-panel-toggle
            wire:loading.attr="disabled"
            :open="$formularioVisible"
            wire:model="formularioVisible"
        />


        {{-- =========================================================
             TABLA PRODUCTOS
        ========================================================== --}}

        <livewire:admin.tabla-productos
            :local-id="$localId"
            :categoria-id="$categoriaId"
            :per-page="$perPage"
            key="tabla-producto-{{ $localId }}-{{ $categoriaId }}"
        />

    </div>


    {{-- =============================================================
         MODAL ELIMINACIÓN
    ============================================================== --}}

    @if($productoIdToDelete)

        <x-confirmation-modal
            wire:model="confirmingProductoDeletion"
            onCancel="cancelDelete"
            onConfirm="delete"
            maxWidth="md"
        >

            <x-slot name="title">
                Confirmar eliminación
            </x-slot>


            <x-slot name="content">
                ¿Está seguro de eliminar este producto?
            </x-slot>


            <x-slot name="footer">

                <div class="flex justify-end gap-2">

                    <button
                        wire:click="cancelDelete"
                        class="btn-secondary"
                    >
                        Cancelar
                    </button>


                    <button
                        wire:click="delete"
                        class="btn-danger"
                    >
                        Eliminar
                    </button>

                </div>

            </x-slot>

        </x-confirmation-modal>

    @endif


    {{-- =============================================================
         MODAL ATRIBUTOS
    ============================================================== --}}

    @if($editAtributosShow)

        <div class="fixed inset-0 bg-gray-800 bg-opacity-75 z-50 flex items-center justify-center dark:text-slate-700">

            <div class="panel w-full max-w-4xl bg-white rounded-lg shadow-lg">

                {{-- HEADER --}}

                <div class="panel-header m-2">

                    <h3 class="text-lg font-semibold">
                        Editando Atributos – Producto {{ $productoEditAtributos->nombre }}
                    </h3>


                    <div class="panel-form-actions flex justify-end mt-4 gap-2">

                        <button
                            type="button"
                            class="btn-danger"
                            wire:click="intentarCerrarModal"
                        >
                            Cancelar
                        </button>

                    </div>

                </div>


                {{-- PANEL IZQUIERDO --}}

                <div class="m-2 bg-white overflow-x-auto">

                    <div class="p-4">

                        <h4 class="font-semibold mb-3 flex items-center gap-2">

                            {{ $atributosSugeridos
                                ? 'Atributos Sugeridos'
                                : 'Atributos Seleccionados' }}

                            @if($atributosSugeridos)

                                <span class="text-xs px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-800">
                                    Sugeridos por la categoría
                                </span>

                            @endif

                        </h4>


                        @livewire(
                            'select-atributo-cards-proxy',
                            [
                                'modelType' => 'producto',
                                'modelId' => $productoIdEditAtributos
                            ],
                            key('cards-'.$productoIdEditAtributos)
                        )

                    </div>

                </div>


                {{-- PANEL DERECHO --}}

                <div class="m-2 panel-form">

                    <form wire:submit.prevent="editAtributos">

                        @livewire(
                            'select-atributo-morph',
                            [
                                'modelType' => 'producto',
                                'modelId' => $productoIdEditAtributos
                            ]
                        )

                    </form>

                </div>

            </div>

        </div>

    @endif

</div>