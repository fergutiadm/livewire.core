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
        <form wire:submit.prevent="save" class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4">
            <div>
                <x-label value="Nombre Completo" />
                <x-input wire:model="name" class="w-full" type="text" />
                <x-input-error for="name" />
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
                <x-label value="Rol de Usuario" />
                <x-select wire:model="role" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500">
                    <option value="">Seleccione un rol...</option>
                    @foreach($roles as $r)
                        <option value="{{ $r->name }}">{{ ucfirst($r->name) }}</option>
                    @endforeach
                </x-select>
                <x-input-error for="role" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4 p-3 bg-slate-50 rounded-lg border border-slate-200">
                <div class="md:col-span-2">
                    <p class="text-xs text-slate-500 italic">
                        {{ $userId ? 'Deje en blanco si no desea cambiar la contraseña actual.' : 'Asigne una contraseña inicial para el nuevo usuario.' }}
                    </p>
                </div>

                <div>
                    <x-label value="Nueva Contraseña" />
                    <x-input wire:model="password" class="w-full" type="password" autocomplete="new-password" />
                </div>

                <div>
                    <x-label value="Confirmar Contraseña" />
                    <x-input wire:model="password_confirmation" class="w-full" type="password" />
                </div>

                <div class="md:col-span-2">
                    <p class="text-xs">
                        <x-input-error for="password" />
                    </p>
                    <p class="text-xs">
                        <x-input-error for="password_confirmation" />
                    </p>
                </div>
            </div>

            <div class="md:col-span-3 flex justify-end gap-3 mt-4 border-t pt-4 border-slate-100">
                <x-button-save-cancel
                        modelId="{{ $userId }}"
                        modelLabel="Usuario"
                >
                </x-button-save-cancel>
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
    <livewire:admin.tabla-usuarios
        :per-page="$perPage"
        :key="'tabla-user-' . time()"
        lazy
    />

    {{-- MODAL DE CONFIRMACIÓN --}}
    @if($userIdToDelete)
    <x-confirmation-modal
        wire:model="confirmingUserDeletion"
        onCancel="cancelDelete"
        onConfirm="delete"
        maxWidth="md"
    >
        <x-slot name="title">Confirmar eliminación</x-slot>
        <x-slot name="content">
            ¿Está seguro de eliminar este usuario? Esta acción no se puede deshacer.
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
