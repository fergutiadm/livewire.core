<div class="container">
    {{-- 1. ÁREA FORMULARIO FLOTANTE --}}
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
        <form wire:submit.prevent="save" class="grid grid-cols-1 md:grid-cols-4 gap-4 p-4">
            <div>
                <x-label value="Nombre Completo" />
                <x-input wire:model="nombre" class="w-full" type="text" />
                <x-input-error for="nombre" />
            </div>

            <div>
                <x-label value="Correo Electrónico" />
                <x-input wire:model="email" class="w-full" type="email" />
                <x-input-error for="email" />
            </div>

            <div>
                <x-label value="Móvil" />
                <x-input wire:model="movil" class="w-full" type="text" />
                <x-input-error for="movil" />
            </div>

            <div>
                <x-label value="Carnet Identidad" />
                <x-input wire:model="ci" class="w-full" type="text" />
                <x-input-error for="ci" />
            </div>

            <div class="md:col-span-3 flex justify-end gap-3 mt-4 border-t pt-4 border-slate-100">
                <!-- Botón Principal (Crear o Actualizar) -->
                <x-button
                    type="submit"
                    class="bg-indigo-600 hover:bg-indigo-700"
                    wire:loading.attr="disabled"
                >
                    <!-- Spinner: Solo visible mientras se ejecuta 'save' -->
                    <svg wire:loading wire:target="save" class="animate-spin h-4 w-4 text-white mr-2" xmlns="http://www.w3.org" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>

                    <!-- Texto Dinámico -->
                    <span wire:loading.remove wire:target="save">
                        {{ $clienteId ? 'Actualizar Cliente' : 'Crear Cliente' }}
                    </span>
                    <span wire:loading wire:target="save">
                        {{ $clienteId ? 'Actualizando...' : 'Creando...' }}
                    </span>
                </x-button>

                <!-- Botón Cancelar / Limpiar -->
                <button
                    type="button"
                    wire:click="cancel"
                    wire:loading.attr="disabled"
                    class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition duration-150 ease-in-out disabled:opacity-50"
                >
                    {{ $clienteId ? 'Cancelar' : 'Limpiar' }}
                </button>
            </div>

        </form>
    </div>
    @endif

    {{-- 2. BOTÓN TOGGLE --}}
    <x-panel-toggle
        wire:loading.attr="disabled"
        :open="$formularioVisible"
        wire:model="formularioVisible"
    />

    {{-- 3. TABLA LISTADO (LAZY) --}}
    <livewire:admin.tabla-clientes
        :per-page="$perPage"
        :key="'tabla-cliente-' . time()"
        lazy
    />

    {{-- MODAL DE CONFIRMACIÓN --}}
    @if($clienteIdToDelete)
    <x-confirmation-modal
        wire:model="confirmingClienteDeletion"
        onCancel="cancelDelete"
        onConfirm="delete"
        maxWidth="md"
    >
        <x-slot name="title">Confirmar eliminación</x-slot>
        <x-slot name="content">
            ¿Está seguro de eliminar este cliente? Esta acción no se puede deshacer.
        </x-slot>
        <x-slot name="footer">
            <div class="flex justify-end gap-2">
                <button wire:click="cancelDelete" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancelar</button>
                <button wire:click="delete" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Eliminar</button>
            </div>
        </x-slot>
    </x-confirmation-modal>
    @endif
</div>
