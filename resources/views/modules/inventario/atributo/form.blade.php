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
    <form
        novalidate
        wire:submit.prevent="save"
        x-on:submit="$dispatch('loading-start', { message: 'Guardando atributo...' })"
        class="space-y-6"
    >

        {{-- DATOS DEL ATRIBUTO --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            {{-- CÓDIGO --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Código
                </label>

                <input
                    type="text"
                    wire:model.live="codigo"
                    class="w-full rounded-xl border-slate-300 uppercase"
                    placeholder="Ej. COLOR"
                    maxlength="100"
                >

                @error('codigo')
                    <p class="panel-form-error">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- NOMBRE --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Nombre
                </label>

                <input
                    type="text"
                    wire:model.live="nombre"
                    class="w-full rounded-xl border-slate-300"
                    placeholder="Nombre del atributo"
                    maxlength="255"
                >

                @error('nombre')
                    <p class="panel-form-error">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- DESCRIPCIÓN --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Descripción
                </label>

                <textarea
                    wire:model.live="descripcion"
                    class="w-full rounded-xl border-slate-300"
                    placeholder="Descripción del atributo"
                    rows="3"
                    maxlength="255"
                ></textarea>

                @error('descripcion')
                    <p class="panel-form-error">
                        {{ $message }}
                    </p>
                @enderror
            </div>

        </div>

        {{-- VALORES --}}
        <div class="border-t border-slate-200 pt-6">

            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-semibold text-slate-800">
                        Valores
                    </h3>

                    <p class="text-xs text-slate-500 mt-1">
                        Valores disponibles para este atributo.
                    </p>
                </div>

                <span class="text-xs text-slate-500">
                    {{ count($valores) }} valor(es)
                </span>
            </div>

            {{-- EDITOR DE VALOR --}}
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">
                            Código
                        </label>

                        <input
                            type="text"
                            wire:model.live="valorCodigo"
                            wire:keydown.enter.prevent="agregarValor"
                            class="w-full rounded-xl border-slate-300 uppercase"
                            placeholder="Ej. ROJO"
                            maxlength="100"
                        >

                        @error('valorCodigo')
                            <p class="panel-form-error">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">
                            Nombre
                        </label>

                        <input
                            type="text"
                            wire:model.live="valorNombre"
                            wire:keydown.enter.prevent="agregarValor"
                            class="w-full rounded-xl border-slate-300"
                            placeholder="Ej. Rojo"
                            maxlength="255"
                        >

                        @error('valorNombre')
                            <p class="panel-form-error">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                </div>

                <div class="flex items-center gap-2 mt-4">

                    <button
                        type="button"
                        wire:click="agregarValor"
                        class="
                            inline-flex
                            items-center
                            rounded-xl
                            bg-indigo-600
                            px-4
                            py-2
                            text-sm
                            font-medium
                            text-white
                            hover:bg-indigo-700
                        "
                    >
                        @if ($valorEditandoIndex !== null)
                            Actualizar valor
                        @else
                            Agregar valor
                        @endif
                    </button>

                    @if ($valorEditandoIndex !== null)
                        <button
                            type="button"
                            wire:click="cancelarEdicionValor"
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
                            Cancelar edición
                        </button>
                    @endif

                </div>

            </div>

            {{-- LISTA DE VALORES --}}
            @if (count($valores) > 0)

                <div class="mt-4 overflow-hidden rounded-xl border border-slate-200">

                    <div
                        class="
                            grid
                            grid-cols-[55px_160px_minmax(0,1fr)_210px]
                            gap-3
                            bg-slate-50
                            px-4
                            py-3
                            text-xs
                            font-semibold
                            uppercase
                            tracking-wide
                            text-slate-500
                        "
                    >
                        <div>#</div>
                        <div>Código</div>
                        <div>Nombre</div>
                        <div class="text-right">Acciones</div>
                    </div>

                    <div class="divide-y divide-slate-200">

                        @foreach ($valores as $index => $valor)

                            <div
                                wire:key="atributo-valor-{{ $valor['id'] ?? 'new-' . $index }}"
                                class="
                                    grid
                                    grid-cols-[55px_160px_minmax(0,1fr)_210px]
                                    gap-3
                                    items-center
                                    px-4
                                    py-3
                                    bg-white
                                "
                            >

                                <div class="text-sm text-slate-500">
                                    {{ $index + 1 }}
                                </div>

                                <div>
                                    <span
                                        class="
                                            inline-flex
                                            rounded-lg
                                            bg-slate-100
                                            px-2
                                            py-1
                                            text-xs
                                            font-semibold
                                            text-slate-700
                                        "
                                    >
                                        {{ $valor['codigo'] }}
                                    </span>
                                </div>

                                <div class="text-sm text-slate-800">
                                    {{ $valor['nombre'] }}
                                </div>

                                <div class="flex items-center justify-end gap-1">

                                    <button
                                        type="button"
                                        wire:click="moverValorArriba({{ $index }})"
                                        @disabled($index === 0)
                                        class="
                                            rounded-lg
                                            border
                                            border-slate-200
                                            px-2
                                            py-1
                                            text-xs
                                            text-slate-600
                                            hover:bg-slate-50
                                            disabled:cursor-not-allowed
                                            disabled:opacity-40
                                        "
                                        title="Subir"
                                    >
                                        ↑
                                    </button>

                                    <button
                                        type="button"
                                        wire:click="moverValorAbajo({{ $index }})"
                                        @disabled($index === count($valores) - 1)
                                        class="
                                            rounded-lg
                                            border
                                            border-slate-200
                                            px-2
                                            py-1
                                            text-xs
                                            text-slate-600
                                            hover:bg-slate-50
                                            disabled:cursor-not-allowed
                                            disabled:opacity-40
                                        "
                                        title="Bajar"
                                    >
                                        ↓
                                    </button>

                                    <button
                                        type="button"
                                        wire:click="editarValor({{ $index }})"
                                        class="
                                            rounded-lg
                                            border
                                            border-yellow-200
                                            bg-yellow-50
                                            px-2
                                            py-1
                                            text-xs
                                            font-medium
                                            text-yellow-700
                                            hover:bg-yellow-100
                                        "
                                    >
                                        Editar
                                    </button>

                                    <button
                                        type="button"
                                        wire:click="eliminarValor({{ $index }})"
                                        wire:confirm="¿Eliminar este valor del atributo?"
                                        class="
                                            rounded-lg
                                            border
                                            border-red-200
                                            bg-red-50
                                            px-2
                                            py-1
                                            text-xs
                                            font-medium
                                            text-red-700
                                            hover:bg-red-100
                                        "
                                    >
                                        Eliminar
                                    </button>

                                </div>
                            </div>

                        @endforeach

                    </div>
                </div>

            @else

                <div
                    class="
                        mt-4
                        rounded-xl
                        border
                        border-dashed
                        border-slate-300
                        bg-slate-50
                        px-4
                        py-8
                        text-center
                    "
                >
                    <p class="text-sm text-slate-500">
                        Este atributo todavía no tiene valores.
                    </p>
                </div>

            @endif

            @error('valores')
                <p class="panel-form-error mt-2">
                    {{ $message }}
                </p>
            @enderror

        </div>

        {{-- ACTIONS --}}
        <div class="flex items-center gap-3 border-t border-slate-200 pt-6">

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