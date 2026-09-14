@php
    $encabezados = ['Nombre', 'DescripciÃ³n'];
@endphp
<div>
    <div class="container">
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
            <form wire:submit="save">
                <div class="panel-form-grid-2">
                    <div class="panel-form-group">
                        <x-label class="panel-form-label">Nombre</x-label>
                        <x-input class="panel-form-input" wire:model="nombre"/>
                        @error('nombre')
                            <p class="panel-form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="panel-form-group">
                        <x-label class="panel-form-label">DescripciÃ³n</x-label>
                        <x-textarea class="panel-form-input" wire:model="descripcion"></x-textarea>
                    </div>
                </div>

                <div class="panel-form-actions">
                    @if($localId)
                        <x-button class="btn-primary-2">Salvar</x-button>
                        <button type="button" wire:click="cancel" class="btn-secondary-2">Cancelar</button>
                    @else
                        <x-button class="btn-primary">Crear</x-button>
                    @endif
                </div>
            </form>
        </div>
        @endif

        <x-panel-toggle wire:loading.attr="disabled"
            :open="$formularioVisible"
            wire:model="formularioVisible"
        />

        <livewire:admin.tabla-locales
            :per-page="$perPage"
            key="'tabla-local-' . time()"
            lazy
        />

    </div>
    {{-- Modal de confirmaciÃ³n --}}
    @if($localIdToDelete)
    <x-confirmation-modal
        wire:model="confirmingLocalDeletion"
        onCancel="cancelDelete"
        onConfirm="delete"
        maxWidth="md"
    >
        {{-- TÃ­tulo personalizado para el slot title --}}
        <x-slot name="title">
            Confirmar eliminaciÃ³n
        </x-slot>

        {{-- Contenido del mensaje para el slot por defecto --}}
        <x-slot name="content">
            Â¿EstÃ¡ seguro de eliminar este local?
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

