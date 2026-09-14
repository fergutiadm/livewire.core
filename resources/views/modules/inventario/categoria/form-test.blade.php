<div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">


    <form
        wire:submit.prevent="save"
        class="space-y-6"
    >

        {{-- ============================================================
             LOCAL
        ============================================================= --}}

        <div>
            <label
                for="localId"
                class="block text-sm font-medium text-slate-700 mb-1"
            >
                Local
            </label>

            <select
                id="localId"
                wire:model="localId"
                class="w-full rounded-xl border-slate-300"
            >
                <option value="">
                    Seleccione un local
                </option>

                @foreach($locales as $local)
                    <option value="{{ $local->id }}">
                        {{ $local->nombre }}
                    </option>
                @endforeach
            </select>
        </div>


        {{-- ============================================================
             NOMBRE
        ============================================================= --}}

        <div>
            <label
                for="nombre"
                class="block text-sm font-medium text-slate-700 mb-1"
            >
                Nombre
            </label>

            <input
                id="nombre"
                type="text"
                wire:model="nombre"
                class="w-full rounded-xl border-slate-300"
            >
        </div>


        {{-- ============================================================
             IMAGEN
        ============================================================= --}}

        <div
            x-data
            class="space-y-4"
        >

            <label
                class="block text-sm font-medium text-slate-700 mb-2"
            >
                Imagen
            </label>


            {{-- ========================================================
                 BOTÓN
            ========================================================= --}}

            <button
                type="button"
                x-on:click="$refs.fileInput.click()"
                class="
                    inline-flex
                    items-center
                    rounded-xl
                    border
                    border-slate-300
                    bg-white
                    px-4
                    py-2
                    text-sm
                    font-medium
                    text-slate-700
                    hover:bg-slate-50
                "
            >
                Agregar imagen
            </button>

            <button
    type="button"
    wire:click="diagnosticarImagenTemporal"
    class="rounded-xl bg-red-600 px-4 py-2 text-white"
>
    Diagnosticar temporal
</button>


            {{-- ========================================================
                 INPUT
            ========================================================= --}}

            <input
                type="file"
                wire:model="imagenes"
                multiple
                wire:key="categoria-media-input-{{ $categoriaId ?? 'nuevo' }}"
                accept="image/jpeg,image/png,image/webp"
                class="hidden"
                x-ref="fileInput"
            >


            {{-- ========================================================
                 CARGANDO
            ========================================================= --}}

            <div
                wire:loading
                wire:target="imagenes"
                class="text-sm text-slate-500"
            >
                Subiendo archivo temporal...
            </div>


            {{-- ========================================================
                 ERRORES
            ========================================================= --}}

            @error('imagenes')
                <div class="text-sm text-red-600">
                    {{ $message }}
                </div>
            @enderror

            @error('imagenes.*')
                <div class="text-sm text-red-600">
                    {{ $message }}
                </div>
            @enderror


            {{-- ========================================================
                 ESTADO DEL ARRAY
            ========================================================= --}}

            <div class="rounded-xl bg-slate-100 p-4 text-sm">

                <div class="font-semibold text-slate-700 mb-2">
                    Estado de $imagenes
                </div>

                @if(empty($imagenes))

                    <div class="text-slate-500">
                        $imagenes está vacío.
                    </div>

                @else

                    <div class="text-green-700 mb-2">
                        $imagenes contiene {{ count($imagenes) }} archivo(s).
                    </div>

                    @foreach($imagenes as $index => $imagen)

                        <div class="border-t border-slate-200 pt-2 mt-2">

                            <div>
                                <strong>Índice:</strong>
                                {{ $index }}
                            </div>

                            <div class="break-all">
                                <strong>Clase:</strong>
                                {{ is_object($imagen) ? get_class($imagen) : gettype($imagen) }}
                            </div>

                            <div class="break-all">
                                <strong>Valor:</strong>
                                {{ is_object($imagen) ? json_encode($imagen) : $imagen }}
                            </div>

                            @if(is_object($imagen) && method_exists($imagen, 'temporaryUrl'))

                                <div class="mt-3">

                                    <div class="font-semibold text-slate-700 mb-2">
                                        temporaryUrl()
                                    </div>

                                    <div class="break-all text-xs text-slate-500 mb-3">
                                        {{ $imagen->temporaryUrl() }}
                                    </div>

                                    <img
                                        src="{{ $imagen->temporaryUrl() }}"
                                        alt="Imagen temporal"
                                        class="
                                            block
                                            max-w-xs
                                            max-h-64
                                            rounded-xl
                                            border
                                            border-slate-300
                                            object-contain
                                        "
                                    >

                                </div>

                            @else

                                <div class="mt-2 text-red-600">
                                    El elemento no tiene método temporaryUrl().
                                </div>

                            @endif

                        </div>

                    @endforeach

                @endif

            </div>


            {{-- ========================================================
                 INFORMACIÓN DEL COMPONENTE
            ========================================================= --}}

            <div class="rounded-xl bg-slate-50 p-4 text-xs text-slate-500">

                <div>
                    <strong>categoriaId:</strong>
                    {{ $categoriaId ?? 'null' }}
                </div>

                <div>
                    <strong>localId:</strong>
                    {{ $localId ?? 'null' }}
                </div>

                <div>
                    <strong>nombre:</strong>
                    {{ $nombre ?? 'null' }}
                </div>

            </div>

        </div>


        {{-- ============================================================
             GUARDAR
        ============================================================= --}}

        <div class="flex justify-end">

            <button
                type="submit"
                class="
                    rounded-xl
                    bg-slate-900
                    px-5
                    py-2.5
                    text-sm
                    font-medium
                    text-white
                    hover:bg-slate-800
                "
            >
                Guardar
            </button>

        </div>

    </form>


    </div>
