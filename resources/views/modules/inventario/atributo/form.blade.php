{{-- resources/views/modules/inventario/atributo/form.blade.php --}}
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

        {{-- ============================================================
            BLOQUES PRINCIPALES 50 / 50
        ============================================================= --}}

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- ========================================================
                ATRIBUTO
            ========================================================= --}}

            <div
                class="
                    rounded-xl
                    border
                    border-slate-200
                    bg-slate-50
                    p-5
                "
            >
                <div class="mb-5">
                    <h3 class="text-base font-semibold text-slate-800">
                        Campos del Atributo
                    </h3>

                    <p class="text-xs text-slate-500 mt-1">
                        Información general del atributo.
                    </p>
                </div>

                <div class="space-y-4">

                    {{-- CÓDIGO --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">
                            Código
                        </label>

                        <input
                            type="text"
                            wire:model.live="codigo"
                            class="
                                w-full
                                rounded-xl
                                border-slate-300
                                bg-white
                                uppercase
                            "
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
                            class="
                                w-full
                                rounded-xl
                                border-slate-300
                                bg-white
                            "
                            placeholder="Ej. Color"
                            maxlength="255"
                        >

                        @error('nombre')
                            <p class="panel-form-error">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- DESCRIPCIÓN --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">
                            Descripción
                        </label>

                        <textarea
                            wire:model.live="descripcion"
                            class="
                                w-full
                                rounded-xl
                                border-slate-300
                                bg-white
                            "
                            placeholder="Descripción del atributo"
                            rows="4"
                            maxlength="255"
                        ></textarea>

                        @error('descripcion')
                            <p class="panel-form-error">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                </div>
            </div>

            {{-- ========================================================
                ATRIBUTO VALORES
            ========================================================= --}}

            <div
                class="
                    rounded-xl
                    border
                    border-slate-200
                    bg-slate-50
                    p-5
                "
            >
                <div class="flex items-start justify-between mb-5">

                    <div>
                        <h3 class="text-base font-semibold text-slate-800">
                            Campos de AtributoValor
                        </h3>

                        <p class="text-xs text-slate-500 mt-1">
                            Valores disponibles para este atributo.
                        </p>
                    </div>

                    <span
                        class="
                            rounded-full
                            bg-indigo-100
                            px-2.5
                            py-1
                            text-xs
                            font-semibold
                            text-indigo-700
                        "
                    >
                        {{ count($valores) }}
                    </span>

                </div>

                {{-- NUEVO / EDITAR VALOR --}}
                <div
                    class="
                        rounded-xl
                        border
                        border-slate-200
                        bg-white
                        p-4
                    "
                >

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        {{-- CÓDIGO --}}
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">
                                Código
                            </label>

                            <input
                                type="text"
                                wire:model.live="valorCodigo"
                                wire:keydown.enter.prevent="agregarValor"
                                class="
                                    w-full
                                    rounded-xl
                                    border-slate-300
                                    uppercase
                                "
                                placeholder="Ej. ROJO"
                                maxlength="100"
                            >

                            @error('valorCodigo')
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
                                wire:model.live="valorNombre"
                                wire:keydown.enter.prevent="agregarValor"
                                class="
                                    w-full
                                    rounded-xl
                                    border-slate-300
                                "
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
                                Cancelar
                            </button>

                        @endif

                    </div>
                </div>

                {{-- ============================================================
                    LISTA DE VALORES - ZONA DRAG & DROP
                ============================================================= --}}

                @if (count($valores) > 0)

                <div class="mt-4">

                    <div class="mb-2 flex items-center justify-between">
                        <span class="text-xs font-medium text-slate-600">
                            Valores
                        </span>

                        <span class="text-xs text-slate-400">
                            Arrastra para cambiar el orden
                        </span>
                    </div>

                    <div
                        wire:sortable="reordenarValores"
                        wire:sortable.options="{ animation: 150 }"
                        class="
                            min-h-[120px]
                            space-y-2
                            rounded-xl
                            border-2
                            border-dashed
                            border-slate-300
                            bg-slate-50
                            p-3
                            transition
                        "
                    >

                        @foreach ($valores as $index => $valor)

                            <div
                                wire:key="atributo-valor-{{ $valor['key'] }}"
                                wire:sortable.item="{{ $valor['key'] }}"
                                class="
                                    flex
                                    items-center
                                    gap-3
                                    rounded-xl
                                    border
                                    border-slate-200
                                    bg-white
                                    px-3
                                    py-3
                                    shadow-sm
                                    transition
                                "
                            >

                                {{-- DRAG HANDLE --}}
                                <div
                                    wire:sortable.handle
                                    class="
                                        flex
                                        h-8
                                        w-8
                                        shrink-0
                                        cursor-move
                                        items-center
                                        justify-center
                                        rounded-lg
                                        bg-slate-100
                                        text-slate-400
                                        hover:bg-slate-200
                                        hover:text-slate-600
                                    "
                                    title="Arrastrar para reordenar"
                                >
                                    <i class="bi bi-grip-vertical"></i>
                                </div>

                                {{-- POSICIÓN --}}
                                <div
                                    class="
                                        flex
                                        h-7
                                        w-7
                                        shrink-0
                                        items-center
                                        justify-center
                                        rounded-full
                                        bg-indigo-100
                                        text-xs
                                        font-semibold
                                        text-indigo-700
                                    "
                                >
                                    {{ $index + 1 }}
                                </div>

                                {{-- CÓDIGO --}}
                                <div class="w-28 shrink-0">
                                    <span
                                        class="
                                            inline-flex
                                            max-w-full
                                            truncate
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

                                {{-- NOMBRE --}}
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm text-slate-800">
                                        {{ $valor['nombre'] }}
                                    </p>
                                </div>

                                {{-- ACCIONES --}}
                                <div class="flex shrink-0 items-center gap-1">

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
                                        wire:click="toggleValorActivo({{ $index }})"
                                        title="Clic para cambiar"
                                        class="
                                            text-xs
                                            font-semibold
                                            px-2
                                            py-0.5
                                            rounded-full
                                            {{
                                                $valor['activo']
                                                    ? 'text-green-700 bg-green-100'
                                                    : 'text-red-700 bg-red-100'
                                            }}
                                        "
                                    >
                                        {{ $valor['activo'] ? 'Retirar' : 'Activar' }}
                                    </button>
                                    {{--  <button
                                        type="button"
                                        wire:click="solicitarEliminacionValor({{ $index }})"
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
                                        Retirar
                                    </button>  --}}

                                </div>

                            </div>

                        @endforeach

                    </div>

                </div>

                @else

                <div
                    class="
                        mt-4
                        min-h-[120px]
                        rounded-xl
                        border-2
                        border-dashed
                        border-slate-300
                        bg-slate-50
                        px-4
                        py-8
                        text-center
                    "
                >
                    <div class="flex flex-col items-center justify-center">

                        <i class="bi bi-list-ul text-2xl text-slate-300"></i>

                        <p class="mt-2 text-sm text-slate-500">
                            Este atributo todavía no tiene valores.
                        </p>

                        <p class="mt-1 text-xs text-slate-400">
                            Agrega un valor utilizando el formulario superior.
                        </p>

                    </div>
                </div>

                @endif

                @error('valores')
                <p class="panel-form-error mt-2">
                    {{ $message }}
                </p>
                @enderror

            </div>

        </div>

        {{-- ============================================================
            ACCIONES GENERALES
        ============================================================= --}}

        <div
            class="
                flex
                items-center
                gap-3
                border-t
                border-slate-200
                pt-6
            "
        >

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
    <x-ui.crud.delete-modal
        :show="$showDeleteValorModal"
        title="Eliminar valor"
        message="¿Está seguro de que desea retirar este valor del atributo?"
        :cancel-method="'cancelarEliminacion'"
        :confirm-method="'confirmarEliminacion'"
        confirm-text="Eliminar"
        loading-message="Eliminando valor..."
    />
</div>