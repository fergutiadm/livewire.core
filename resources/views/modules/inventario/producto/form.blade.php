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
        x-on:submit="$dispatch('loading-start', { message: 'Guardando producto...' })"
        class="space-y-6"
    >

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

                    @foreach($locales as $local)

                        <option value="{{ $local->id }}">
                            {{ $local->nombre }}
                        </option>

                    @endforeach

                </select>

                @error('localId')
                    <p class="panel-form-error">{{ $message }}</p>
                @enderror

            </div>

            {{-- CATEGORIA --}}
            <div>

                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Categoría
                </label>

                <select
                    wire:model="categoriaId"
                    wire:change="categoriaChanged"
                    class="w-full rounded-xl border-slate-300"
                >

                    <option value="">
                        Todas
                    </option>

                    @foreach($categorias as $categoria)

                        <option value="{{ $categoria->id }}">
                            {{ $categoria->nombre }}
                        </option>

                    @endforeach

                </select>
                @error('categoriaId')<p class="panel-form-error">{{ $message }}</p>@enderror
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
                    placeholder="Nombre del producto"
                >
                @error('nombre')<p class="panel-form-error">{{ $message }}</p>@enderror
            </div>

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

        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

            {{-- COSTO --}}
            <div>

                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Costo
                </label>

                <input
                    type="number"
                    step="0.01"
                    wire:model="costo"
                    class="w-full rounded-xl border-slate-300"
                    placeholder="Costo de adquisición"
                >

                @error('costo')<p class="panel-form-error">{{ $message }}</p>@enderror
            </div>


            {{-- PRECIO --}}
            <div>

                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Precio
                </label>

                <input
                    type="number"
                    step="0.01"
                    wire:model="precio"
                    class="w-full rounded-xl border-slate-300"
                    placeholder="Precio de venta"
                >

                @error('precio')<p class="panel-form-error">{{ $message }}</p>@enderror

            </div>

            {{-- DESCUENTO --}}
            <div>

                <label class="block text-sm font-medium text-slate-700 mb-1">
                    % Descuento
                </label>

                <input
                    type="number"
                    step="0.01"
                    wire:model="porciento_descuento"
                    class="w-full rounded-xl border-slate-300"
                >

                @error('porciento_descuento')<p class="panel-form-error">{{ $message }}</p>@enderror

            </div>

        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            {{-- CODIGO --}}
            <div>

                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Código
                </label>

                <input
                    type="text"
                    wire:model="codigo"
                    class="w-full rounded-xl border-slate-300"
                    placeholder="Se genera automáticamente"
                >

                @error('codigo')<p class="panel-form-error">{{ $message }}</p>@enderror

            </div>

            <div class="panel-form-group">

                <x-label class="panel-form-label">

                    Código generado

                    @if($codigo)

                        <a
                            href="{{ route('barcode.preview', ['code' => $codigo]) }}"
                            download="barcode_{{ $codigo }}.png"
                            class="cursor-pointer inline-flex items-center gap-1 text-xs text-slate-50 whitespace-nowrap font-bold bg-black/60 px-2 rounded-full"
                        >
                            DESCARGAR
                        </a>

                    @endif

                </x-label>


                <div class="panel-form-input bg-gray-100 flex items-center justify-center">

                    @if($codigo)

                        <a
                            href="{{ route('barcode.preview', ['code' => $codigo]) }}"
                            download="barcode_{{ $codigo }}.png"
                        >
                            <img
                                src="{{ route('barcode.preview', ['code' => $codigo]) }}"
                                class="h-16 cursor-pointer"
                                alt="Código de barras {{ $codigo }}"
                            >
                        </a>

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
            <a
            href="{{ route('barcode.zpl', [
                'code' => $codigo,
                'name' => $nombre,
            ]) }}"
            class="btn-secondary flex items-center justify-center gap-2 px-4 py-2 whitespace-nowrap"
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
            </a>

            </div>

        @endif

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

        <livewire:inventario.producto.media-manager
            :producto-id="$productoId"
            :key="'producto-media-manager-'.$productoId"
        />

    {{-- ============================================================
        ATTRIBUTES MANAGER
    ============================================================= --}}

        <livewire:inventario.producto.attributes-manager
            :producto-id="$productoId"
             wire:key="producto-attributes-manager-{{ $productoId }}"
        />
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
    {{--  producto-media-component.blade.php  --}}
</div>
