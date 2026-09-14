<x-confirmation-fert-modal
    wire:model="confirmingProductDeletion"
    onCancel="cancelDelete"
    onConfirm="delete"
>
    {{-- Título personalizado para el slot title --}}
    <x-slot name="title">
        Confirmar eliminación
    </x-slot>

    {{-- Contenido del mensaje para el slot por defecto --}}
    ¿Está seguro de eliminar este producto?
</x-confirmation-fert-modal>
