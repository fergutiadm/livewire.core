<div class="panel-listado bg-white overflow-x-auto relative">

    {{-- Overlay de carga --}}
    <div
        wire:loading.delay.shortest
        wire:target="recargarProductos, perPage, gotoPage, nextPage, previousPage"
        class="absolute inset-0 z-20 bg-white/60 backdrop-blur-[2px] flex items-center justify-center"
    >
        <div class="flex flex-col items-center gap-2">

            <svg
                class="animate-spin h-8 w-8 text-slate-600"
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
                ></circle>

                <path
                    class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                ></path>
            </svg>

            <span class="text-slate-800 font-medium text-sm">
                Actualizando listado...
            </span>

        </div>
    </div>


    {{-- TABLA --}}
    <table class="w-full text-sm min-w-[700px]">

        {{-- CABECERA --}}
        <thead>
            <tr class="bg-gray-50 text-gray-600 text-left font-medium">

                <th class="px-4 py-3">
                    Nombre
                </th>

                <th class="px-4 py-3">
                    Costo
                </th>

                <th class="px-4 py-3">
                    Precio
                </th>

                <th class="px-4 py-3">
                    Descuento
                </th>

                <th class="px-4 py-3 text-right">
                    Acciones
                </th>

            </tr>
        </thead>


        {{-- CUERPO --}}
        <tbody>

            @forelse($productos as $producto)

                <tr
                    class="border-b hover:bg-gray-50 transition"
                    wire:key="producto-{{ $producto->id }}"
                >

                    <td class="px-4 py-3">
                        {{ $producto->nombre }}
                    </td>

                    {{-- Categoría desactivada por ahora --}}
                    {{--
                    <td class="px-4 py-3">
                        {{ $producto->categoria?->nombre }}
                    </td>
                    --}}

                    <td class="px-4 py-3">
                        {{ number_format($producto->costo, 2) }}
                    </td>

                    <td class="px-4 py-3">
                        {{ number_format($producto->precio, 2) }}
                    </td>

                    <td class="px-4 py-3">
                        {{ $producto->porciento_descuento }}%
                    </td>

                    <td class="px-4 py-3 text-right">

                        <div class="flex gap-2 justify-end">

                            {{-- EDITAR --}}
                            <x-icon-button
                                variant="edit"
                                click="edit({{ $producto->id }})"
                            />

                            {{-- ELIMINAR --}}
                            <div
                                wire:loading.class="opacity-50 pointer-events-none"
                                wire:target="confirmDelete({{ $producto->id }})"
                            >
                                <x-icon-button
                                    variant="delete"
                                    click="confirmDelete({{ $producto->id }})"
                                />
                            </div>

                            {{-- ATRIBUTOS --}}
                            <x-icon-button
                                variant="atributos"
                                click="editAtributos({{ $producto->id }})"
                            />

                        </div>

                    </td>

                </tr>

            @empty

                <tr>
                    <td
                        colspan="5"
                        class="px-4 py-10 text-center italic text-slate-500"
                    >
                        Nada para mostrar
                    </td>
                </tr>

            @endforelse

        </tbody>

    </table>


    {{-- PAGINACIÓN --}}
    @if($productos->hasPages())

        <div class="mt-4">

            <x-ui.livewire.pagination
                :paginator="$productos"
                :window="2"
                :show-summary="true"
            >

                <x-slot:middle>

                    <x-ui.livewire.per-page
                        wire:model="perPage"
                        class="hidden sm:inline-flex"
                    />

                </x-slot:middle>

            </x-ui.livewire.pagination>

        </div>

    @endif

</div>