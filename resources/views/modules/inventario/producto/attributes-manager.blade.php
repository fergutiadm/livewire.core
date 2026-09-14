<div>

    @if($show)

        {{-- ============================================================
            OVERLAY
        ============================================================= --}}
        <div
            class="fixed inset-0 z-50 flex items-center justify-center bg-gray-800/75 p-4"
        >

            {{-- ========================================================
                MODAL
            ========================================================= --}}
            <div
                class="flex w-full max-w-5xl max-h-[90vh] flex-col overflow-hidden rounded-xl bg-white shadow-2xl"
            >

                {{-- ====================================================
                    HEADER
                ===================================================== --}}
                <div
                    class="flex shrink-0 items-center justify-between border-b border-gray-200 px-6 py-4"
                >
                    <div class="min-w-0">

                        <h2
                            class="text-xl font-semibold text-gray-800"
                        >
                            Editar atributos del producto
                        </h2>

                        @if($productoNombre)
                            <p class="mt-1 text-sm text-gray-500">
                                Producto: {{ $productoNombre }}
                            </p>
                        @endif

                    </div>

                    <button
                        type="button"
                        wire:click="cerrar"
                        class="ml-4 shrink-0 text-2xl leading-none text-gray-400 transition hover:text-gray-700"
                        title="Cerrar"
                    >
                        &times;
                    </button>
                </div>


                {{-- ====================================================
                    CONTENIDO
                ===================================================== --}}
                <div
                    class="min-h-0 flex-1 overflow-y-auto px-6 py-6"
                >

                    <div
                        class="grid w-full grid-cols-1 gap-6 lg:grid-cols-2"
                    >

                        {{-- ==================================================
                            ATRIBUTOS ACTUALES DEL PRODUCTO
                        =================================================== --}}
                        <section
                            class="w-full min-w-0 overflow-hidden rounded-lg border border-gray-200 bg-white"
                        >

                            <div
                                class="border-b border-gray-200 bg-gray-50 px-4 py-3"
                            >
                                <h3
                                    class="text-sm font-semibold uppercase tracking-wide text-gray-700"
                                >
                                    Atributos del producto
                                </h3>
                            </div>


                            <div class="space-y-4 p-4">

                                @forelse($cards as $atributoId => $card)

                                    {{-- ==================================================
                                        CARD DEL ATRIBUTO
                                    =================================================== --}}
                                    <div
                                        wire:key="producto-atributo-card-{{ $atributoId }}"
                                        x-data="{ dragging: false }"
                                        draggable="true"

                                        @dragstart.stop="
                                            dragging = true;
                                            $event.dataTransfer.effectAllowed = 'move';
                                            $event.dataTransfer.setData(
                                                'atributo-id',
                                                '{{ $atributoId }}'
                                            );
                                        "

                                        @dragend.stop="
                                            dragging = false;
                                        "

                                        @dragover.prevent.stop="
                                            $event.dataTransfer.dropEffect = 'move';
                                        "

                                        @drop.prevent.stop="
                                            const origenAtributo = parseInt(
                                                $event.dataTransfer.getData('atributo-id')
                                            );

                                            const destinoAtributo = {{ $atributoId }};

                                            if (
                                                origenAtributo !== destinoAtributo
                                            ) {
                                                $wire.reordenarAtributos(
                                                    origenAtributo,
                                                    destinoAtributo
                                                );
                                            }
                                        "

                                        :class="{
                                            'opacity-50': dragging
                                        }"

                                        class="w-full min-w-0 cursor-move rounded-lg border border-gray-200 bg-white p-4 shadow-sm transition"
                                    >

                                        {{-- ====================================
                                            CABECERA DEL ATRIBUTO
                                        ===================================== --}}
                                        <div
                                            class="flex items-center justify-between gap-3"
                                        >

                                            <div class="min-w-0">

                                                {{-- Indicador visual de Drag & Drop --}}
                                                <span
                                                    class="cursor-grab select-none text-gray-400 active:cursor-grabbing"
                                                    title="Arrastrar para reordenar"
                                                >
                                                    ⋮⋮
                                                </span>

                                                <span
                                                    class="font-semibold text-gray-800"
                                                >
                                                    {{ $card['atributo'] }}
                                                </span>

                                                <span
                                                    class="text-xs text-gray-400"
                                                >
                                                    orden: {{ $card['orden_visual'] }}
                                                </span>
                                            </div>


                                            <div
                                                class="flex shrink-0 items-center gap-2"
                                            >

                                                {{-- SUBIR ATRIBUTO --}}
                                                <button
                                                    type="button"
                                                    wire:click="moverAtributoArriba({{ $atributoId }})"
                                                    @disabled($loop->first)
                                                    @class([
                                                                'inline-flex h-7 w-7 items-center justify-center rounded border text-xs transition',
                                                                'border-gray-200 bg-white text-gray-600 hover:bg-gray-100' => !$loop->first,
                                                                'cursor-not-allowed border-gray-100 bg-gray-100 text-gray-300' => $loop->first,
                                                            ])
                                                    title="Subir atributo"
                                                >
                                                    ▲
                                                </button>


                                                {{-- BAJAR ATRIBUTO --}}
                                                <button
                                                    type="button"
                                                    wire:click="moverAtributoAbajo({{ $atributoId }})"
                                                    @disabled($loop->last)
                                                    @class([
                                                                'inline-flex h-7 w-7 items-center justify-center rounded border text-xs transition',
                                                                'border-gray-200 bg-white text-gray-600 hover:bg-gray-100' => !$loop->last,
                                                                'cursor-not-allowed border-gray-100 bg-gray-100 text-gray-300' => $loop->last,
                                                            ])
                                                    title="Bajar atributo"
                                                >
                                                    ▼
                                                </button>


                                                {{-- ELIMINAR ATRIBUTO --}}
                                                <button
                                                    type="button"
                                                    wire:click="eliminarAtributo({{ $atributoId }})"
                                                    class="ml-2 text-sm font-medium text-red-600 transition hover:text-red-800"
                                                >
                                                    Eliminar
                                                </button>

                                            </div>

                                        </div>


                                        {{-- ====================================
                                            VALORES DEL ATRIBUTO
                                        ===================================== --}}
                                        @if(!empty($card['valores']))

                                            <ul
                                                class="mt-3 space-y-2"
                                            >

                                                @foreach($card['valores'] as $valorId => $valor)

                                                    <li
                                                        wire:key="producto-atributo-{{ $atributoId }}-valor-{{ $valorId }}"

                                                        x-data="{ dragging: false }"

                                                        draggable="true"

                                                        {{-- DRAG START --}}
                                                        @dragstart.stop="
                                                            dragging = true;

                                                            $event.dataTransfer.effectAllowed = 'move';

                                                            $event.dataTransfer.setData(
                                                                'valor-id',
                                                                '{{ $valorId }}'
                                                            );

                                                            $event.dataTransfer.setData(
                                                                'atributo-id',
                                                                '{{ $atributoId }}'
                                                            );
                                                        "

                                                        {{-- DRAG END --}}
                                                        @dragend.stop="
                                                            dragging = false;
                                                        "

                                                        {{-- DRAG OVER --}}
                                                        @dragover.prevent.stop="
                                                            $event.dataTransfer.dropEffect = 'move';
                                                        "

                                                        {{-- DROP --}}
                                                        @drop.prevent.stop="
                                                            const origenAtributo = parseInt(
                                                                $event.dataTransfer.getData('atributo-id')
                                                            );

                                                            const origenValor = parseInt(
                                                                $event.dataTransfer.getData('valor-id')
                                                            );

                                                            const destinoAtributo = {{ $atributoId }};

                                                            const destinoValor = {{ $valorId }};

                                                            if (
                                                                origenAtributo === destinoAtributo &&
                                                                origenValor !== destinoValor
                                                            ) {
                                                                $wire.reordenarValores(
                                                                    destinoAtributo,
                                                                    origenValor,
                                                                    destinoValor
                                                                );
                                                            }
                                                        "

                                                        :class="{
                                                            'opacity-50': dragging
                                                        }"

                                                        class="flex w-full cursor-move items-center justify-between gap-3 rounded-md border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-700 transition hover:bg-gray-100"
                                                    >

                                                        {{-- ====================================
                                                            INFORMACIÓN DEL VALOR
                                                        ===================================== --}}
                                                        <div
                                                            class="flex min-w-0 items-center gap-2"
                                                        >

                                                            {{-- Indicador visual de Drag & Drop --}}
                                                            <span
                                                                class="cursor-grab select-none text-gray-400 active:cursor-grabbing"
                                                                title="Arrastrar para reordenar"
                                                            >
                                                                ⋮⋮
                                                            </span>

                                                            <span>
                                                                {{ $valor['valor'] }}
                                                            </span>

                                                            <span
                                                                class="text-xs text-gray-400"
                                                            >
                                                                orden: {{ $valor['orden_visual'] }}
                                                            </span>

                                                        </div>


                                                        {{-- ====================================
                                                            ACCIONES DEL VALOR
                                                        ===================================== --}}
                                                        <div
                                                            class="flex shrink-0 items-center gap-2"
                                                        >

                                                            {{-- MOVER ARRIBA --}}
                                                            <button
                                                                type="button"
                                                                wire:click="moverValorArriba({{ $atributoId }}, {{ $valorId }})"
                                                                @disabled($loop->first)
                                                                @class([
                                                                    'inline-flex h-7 w-7 items-center justify-center rounded border text-xs transition',
                                                                    'border-gray-200 bg-white text-gray-600 hover:bg-gray-100' => !$loop->first,
                                                                    'cursor-not-allowed border-gray-100 bg-gray-100 text-gray-300' => $loop->first,
                                                                ])
                                                                title="Mover arriba"
                                                            >
                                                                ▲
                                                            </button>


                                                            {{-- MOVER ABAJO --}}
                                                            <button
                                                                type="button"
                                                                wire:click="moverValorAbajo({{ $atributoId }}, {{ $valorId }})"
                                                                @disabled($loop->last)
                                                                @class([
                                                                    'inline-flex h-7 w-7 items-center justify-center rounded border text-xs transition',
                                                                    'border-gray-200 bg-white text-gray-600 hover:bg-gray-100' => !$loop->last,
                                                                    'cursor-not-allowed border-gray-100 bg-gray-100 text-gray-300' => $loop->last,
                                                                ])
                                                                title="Mover abajo"
                                                            >
                                                                ▼
                                                            </button>


                                                            {{-- QUITAR VALOR --}}
                                                            <button
                                                                type="button"
                                                                wire:click="eliminarValor({{ $atributoId }}, {{ $valorId }})"
                                                                class="text-sm font-medium text-red-600 transition hover:text-red-800"
                                                            >
                                                                Quitar
                                                            </button>

                                                        </div>

                                                    </li>

                                                @endforeach

                                            </ul>

                                        @else

                                            <div
                                                class="mt-3 text-sm italic text-gray-400"
                                            >
                                                No hay valores asociados.
                                            </div>

                                        @endif

                                    </div>

                                @empty

                                    <div
                                        class="rounded-lg border border-dashed border-gray-300 p-6 text-center text-sm text-gray-500"
                                    >
                                        Este producto no tiene atributos asociados.
                                    </div>

                                @endforelse

                            </div>

                        </section>



                        {{-- ==================================================
                            GESTIÓN DE ATRIBUTOS
                        =================================================== --}}
                        <section
                            class="w-full min-w-0 overflow-hidden rounded-lg border border-gray-200 bg-white"
                        >

                            <div
                                class="border-b border-gray-200 bg-gray-50 px-4 py-3"
                            >
                                <h3
                                    class="text-sm font-semibold uppercase tracking-wide text-gray-700"
                                >
                                    Gestión de atributos
                                </h3>
                            </div>


                            <div class="space-y-8 p-4">


                                {{-- ==================================================
                                    AGREGAR ATRIBUTO NUEVO
                                =================================================== --}}
                                <div class="space-y-5">

                                    <div>

                                        <h4
                                            class="text-sm font-semibold text-gray-800"
                                        >
                                            Agregar atributo
                                        </h4>

                                        <p
                                            class="mt-1 text-xs text-gray-500"
                                        >
                                            Agregue un atributo que todavía no pertenece
                                            al producto.
                                        </p>

                                    </div>


                                    {{-- ATRIBUTO --}}
                                    <div class="w-full">

                                        <label
                                            for="producto-atributo-selector"
                                            class="mb-1 block text-sm font-medium text-gray-700"
                                        >
                                            Atributo
                                        </label>

                                        <select
                                            id="producto-atributo-selector"
                                            wire:model.live="selectedAtributoId"
                                            class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                                        >

                                            <option value="">
                                                Seleccione un atributo
                                            </option>

                                            @foreach($availableAtributos as $atributoId => $atributoNombre)

                                                <option value="{{ $atributoId }}">
                                                    {{ $atributoNombre }}
                                                </option>

                                            @endforeach

                                        </select>

                                    </div>


                                    {{-- VALORES --}}
                                    <div class="w-full">

                                        <label
                                            class="mb-2 block text-sm font-medium text-gray-700"
                                        >
                                            Valores
                                        </label>


                                        @if($selectedAtributoId)

                                            @if(!empty($availableValores))

                                                <div class="space-y-2">

                                                    @foreach($availableValores as $valorId => $valorData)

                                                        <label
                                                            wire:key="producto-nuevo-valor-{{ $selectedAtributoId }}-{{ $valorId }}"
                                                            class="flex w-full cursor-pointer items-center gap-2 rounded-md border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                                        >

                                                            <input
                                                                type="checkbox"
                                                                value="{{ $valorId }}"
                                                                wire:model.live="selectedValores"
                                                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                                            >

                                                            <span>
                                                                {{ $valorData['valor'] }}
                                                            </span>

                                                        </label>

                                                    @endforeach

                                                </div>

                                            @else

                                                <div
                                                    class="rounded-md border border-dashed border-gray-300 px-3 py-4 text-center text-sm text-gray-400"
                                                >
                                                    Este atributo no tiene valores definidos.
                                                </div>

                                            @endif

                                        @else

                                            <div
                                                class="rounded-md border border-dashed border-gray-300 px-3 py-4 text-center text-sm text-gray-400"
                                            >
                                                Seleccione un atributo para ver sus valores.
                                            </div>

                                        @endif

                                    </div>


                                    {{-- BOTÓN --}}
                                    <div class="flex justify-end">

                                        <button
                                            type="button"
                                            wire:click="agregarAtributo"
                                            @disabled(
                                                !$selectedAtributoId ||
                                                empty($selectedValores)
                                            )
                                            @class([
                                                'btn-primary',
                                                'opacity-50 cursor-not-allowed' =>
                                                    !$selectedAtributoId ||
                                                    empty($selectedValores),
                                            ])
                                        >
                                            Agregar atributo
                                        </button>

                                    </div>

                                </div>

                                {{-- ============================================================
                                    Atributos sugeridos por la categoría
                                    ============================================================ --}}
                                <div class="mt-6">

                                    <h3 class="text-sm font-semibold text-gray-700 mb-2">
                                        Atributos sugeridos por la categoría
                                    </h3>

                                    <select
                                        wire:model.live="selectedSuggestedAtributoId"
                                        class="w-full rounded-md border-gray-300 text-sm"
                                    >
                                        <option value="">
                                            Seleccionar atributo sugerido...
                                        </option>

                                        @foreach($suggestedAtributos as $atributoId => $atributoNombre)
                                            <option value="{{ $atributoId }}">
                                                {{ $atributoNombre }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @if($selectedSuggestedAtributoId)

                                        <div class="mt-3 space-y-2">

                                            @foreach($availableSuggestedValores as $valorId => $valor)

                                                <label
                                                    wire:key="producto-suggested-valor-{{ $selectedSuggestedAtributoId }}-{{ $valorId }}"
                                                    class="flex items-center gap-2 text-sm"
                                                >
                                                    <input
                                                        type="checkbox"
                                                        wire:model.live="selectedSuggestedValores"
                                                        value="{{ $valorId }}"
                                                        class="rounded border-gray-300"
                                                    >

                                                    <span>
                                                        {{ $valor['valor'] }}
                                                    </span>
                                                </label>

                                            @endforeach

                                        </div>

                                        <button
                                            type="button"
                                            wire:click="agregarAtributoSugerido"
                                            @disabled(
                                                !$selectedSuggestedAtributoId ||
                                                empty($selectedSuggestedValores)
                                            )
                                            class="mt-3 px-3 py-2 rounded-md text-sm font-medium
                                                bg-gray-200 text-gray-500
                                                disabled:opacity-50 disabled:cursor-not-allowed
                                                enabled:bg-gray-800 enabled:text-white"
                                        >
                                            Agregar atributo sugerido
                                        </button>

                                    @endif

                                </div>


                                {{-- ==================================================
                                    AGREGAR VALORES A ATRIBUTO EXISTENTE
                                =================================================== --}}
                                <div
                                    class="space-y-5 border-t border-gray-200 pt-6"
                                >

                                    <div>

                                        <h4
                                            class="text-sm font-semibold text-gray-800"
                                        >
                                            Agregar valores a atributo existente
                                        </h4>

                                        <p
                                            class="mt-1 text-xs text-gray-500"
                                        >
                                            Seleccione un atributo del producto para
                                            agregarle valores adicionales.
                                        </p>

                                    </div>


                                    {{-- ATRIBUTO EXISTENTE --}}
                                    <div class="w-full">

                                        <label
                                            for="producto-atributo-existente-selector"
                                            class="mb-1 block text-sm font-medium text-gray-700"
                                        >
                                            Atributo
                                        </label>

                                        <select
                                            id="producto-atributo-existente-selector"
                                            wire:model.live="selectedExistingAtributoId"
                                            class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                                        >

                                            <option value="">
                                                Seleccione un atributo
                                            </option>

                                            @foreach($availableExistingAtributos as $atributoId => $atributoNombre)

                                                <option value="{{ $atributoId }}">
                                                    {{ $atributoNombre }}
                                                </option>

                                            @endforeach

                                        </select>

                                    </div>


                                    {{-- VALORES EXISTENTES --}}
                                    <div class="w-full">

                                        <label
                                            class="mb-2 block text-sm font-medium text-gray-700"
                                        >
                                            Valores
                                        </label>


                                        @if($selectedExistingAtributoId)

                                            @if(!empty($availableExistingValores))

                                                <div class="space-y-2">

                                                    @foreach($availableExistingValores as $valorId => $valorData)

                                                        <label
                                                            wire:key="producto-existente-valor-{{ $selectedExistingAtributoId }}-{{ $valorId }}"
                                                            @class([
                                                                'flex w-full items-center gap-2 rounded-md border px-3 py-2 text-sm',
                                                                'cursor-pointer border-gray-200 bg-gray-50 text-gray-700 hover:bg-gray-100'
                                                                    => !$valorData['asociado'],
                                                                'cursor-not-allowed border-gray-200 bg-gray-100 text-gray-400'
                                                                    => $valorData['asociado'],
                                                            ])
                                                        >

                                                            <input
                                                                type="checkbox"
                                                                value="{{ $valorId }}"
                                                                wire:model.live="selectedExistingValores"
                                                                @disabled($valorData['asociado'])
                                                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                                            >

                                                            <span>
                                                                {{ $valorData['valor'] }}
                                                            </span>

                                                            @if($valorData['asociado'])

                                                                <span
                                                                    class="ml-auto text-xs text-gray-400"
                                                                >
                                                                    Asociado
                                                                </span>

                                                            @endif

                                                        </label>

                                                    @endforeach

                                                </div>

                                            @else

                                                <div
                                                    class="rounded-md border border-dashed border-gray-300 px-3 py-4 text-center text-sm text-gray-400"
                                                >
                                                    Este atributo no tiene valores definidos.
                                                </div>

                                            @endif

                                        @else

                                            <div
                                                class="rounded-md border border-dashed border-gray-300 px-3 py-4 text-center text-sm text-gray-400"
                                            >
                                                Seleccione un atributo para ver sus valores.
                                            </div>

                                        @endif

                                    </div>


                                    {{-- BOTÓN --}}
                                    <div class="flex justify-end">

                                        <button
                                            type="button"
                                            wire:click="agregarValores"
                                            @disabled(
                                                !$selectedExistingAtributoId ||
                                                empty($selectedExistingValores)
                                            )
                                            @class([
                                                'btn-primary',
                                                'opacity-50 cursor-not-allowed' =>
                                                    !$selectedExistingAtributoId ||
                                                    empty($selectedExistingValores),
                                            ])
                                        >
                                            Agregar valores
                                        </button>

                                    </div>

                                </div>

                            </div>

                        </section>

                    </div>

                </div>



                {{-- ====================================================
                    FOOTER
                ===================================================== --}}
                <div
                    class="flex shrink-0 items-center justify-end gap-3 border-t border-gray-200 bg-white px-6 py-4"
                >

                    <button
                        type="button"
                        wire:click="diagnosticarCambios"
                        class="btn-secondary"
                    >
                        Diagnosticar cambios
                    </button>


                    <button
                        type="button"
                        wire:click="cerrar"
                        class="btn-secondary"
                    >
                        Cerrar
                    </button>


                    <button
                        type="button"
                        wire:click="guardar"
                        @disabled(!$this->hayCambios())
                        @class([
                            'btn-primary',
                            'opacity-50 cursor-not-allowed' => !$this->hayCambios(),
                        ])
                    >
                        Guardar
                    </button>

                </div>

            </div>

        </div>

    @endif

</div>