<div>
    <div class="space-y-6">

        {{-- HEADER --}}
        <div class="flex items-center justify-between">

            <div>
                <h1 class="text-2xl font-bold text-slate-800">
                    Productos
                </h1>

                <p class="text-sm text-slate-500">
                    Gestión de productos
                </p>
            </div>

            <button
                wire:click="toggleForm"
                class="inline-flex items-center gap-2 rounded-xl bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700"
            >
                {{ $showForm ? 'Ocultar formulario' : 'Mostrar formulario' }}
            </button>

        </div>

        {{-- FORM (NO DESTRUCTIVO) --}}
        <div @class([
            'transition-all duration-200',
            'hidden' => ! $showForm,
        ])>

       <livewire:inv.producto.form />

        </div>

        {{-- TABLE --}}
        <livewire:inv.producto.table
            :local-id="$localId"
            :categoria-id="$categoriaId"
            lazy
            wire:key="producto-table-{{ $tableVersion }}"
        />

    </div>
    @if($showDeleteModal)

        <div class="fixed inset-0 z-50 flex items-center justify-center">

            <div class="absolute inset-0 bg-black/50"></div>

            <div class="relative w-full max-w-md rounded-xl bg-white p-6 shadow-xl">

                <h2 class="text-lg font-semibold">
                    Confirmar eliminación
                </h2>

                <p class="mt-2 text-sm text-gray-600">
                    ¿Está seguro de que desea eliminar este producto?
                </p>

                <div class="mt-6 flex justify-end gap-3">

                    <button
                        type="button"
                        wire:click="cancelarEliminacion"
                        class="rounded-lg bg-gray-200 px-4 py-2 text-sm"
                    >
                        Cancelar
                    </button>

                    <button
                        type="button"
                        wire:click="confirmarEliminacion"
                        class="rounded-lg bg-red-600 px-4 py-2 text-sm text-white"
                    >
                        Eliminar
                    </button>

                </div>

            </div>

        </div>

    @endif
</div>
