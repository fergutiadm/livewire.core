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
            <div class="panel-form-grid-3">
                <div class="">
                    <h3 class="font-semibold mb-2 shadow-lg text-slate-700 bg-slate-300 bg-opacity-30 rounded-lg p-2 m-2">Datos del Atributo</h3>
                    <form wire:submit.prevent="save">
                        <div class="panel-form-group">
                            <x-label class="panel-form-label">Nombre</x-label>
                            <x-input class="panel-form-input" wire:model="nombre" />
                            @error('nombre') <p class="panel-form-error">{{ $message }}</p> @enderror
                        </div>

                        <div class="panel-form-group">
                            <x-label class="panel-form-label">DescripciÃ³n</x-label>
                            <x-textarea class="panel-form-input" wire:model="descripcion"></x-textarea>
                        </div>

                        <div class="panel-form-actions">
                            <x-button class="btn-primary-2">{{ $atributoId ? 'Salvar' : 'Crear' }}</x-button>
                            @if($atributoId)
                            <button type="button" wire:click="cancel" class="btn-secondary-2">Cancelar</button>
                            @endif
                        </div>
                    </form>
                </div>

                {{-- Valores del Atributo --}}
                @if($showValores && $atributoId)
                <div class="">
                    <h3 class="font-semibold mb-2 text-gray-500 rounded-lg p-2 m-2">Valores del Atributo</h3>

                    {{-- Formulario Valor --}}
                    <form wire:submit.prevent="saveValor" class="mb-4">
                        <div class="panel-form-group">
                            <x-label class="panel-form-label text-gray-400">Valor</x-label>
                            <x-input class="panel-form-input text-gray-400" wire:model="valor" />
                            @error('valor') <p class="panel-form-error">{{ $message }}</p> @enderror
                        </div>

                        <div class="panel-form-group">
                            <x-label class="panel-form-label">DescripciÃ³n</x-label>
                            <x-textarea class="panel-form-input" wire:model="valorDescripcion"></x-textarea>
                        </div>

                        <div class="panel-form-actions">
                            <x-button class="btn-primary">{{ $valorId ? 'Salvar' : 'Crear' }}</x-button>
                            @if($valorId)
                            <button type="button" wire:click="resetValoresForm" class="btn-secondary">Cancelar</button>
                            @endif
                        </div>
                    </form>
                </div>
                <div class="">
                    {{-- Lista Drag & Drop de Valores --}}
                    <ul wire:sortable="reorderValores" wire:sortable.options="{ animation: 150 }" class="space-y-2">
                        @foreach($valores as $valorItem)
                        <li wire:sortable.item="{{ $valorItem['id'] }}" wire:key="valor-{{ $valorItem['id'] }}"
                            class="flex justify-between items-center bg-slate-500 bg-opacity-20 p-2 rounded">

                            <div class="flex items-center gap-2">
                                {{-- Handle --}}
                                <span wire:sortable.handle class="cursor-move text-gray-400 flex-none">
                                    <i class="bi bi-grip-vertical"></i>
                                </span>

                                <div>
                                    <span class="font-medium text-slate-600">{{ $valorItem['valor'] }}</span>
                                    <span class="block text-sm text-slate-500">{{ $valorItem['descripcion'] }}</span>
                                </div>
                            </div>

                            <div class="flex gap-2">
                                <x-icon-button variant="edit" click="editValor({{ $valorItem['id'] }})"/>
                                <x-icon-button variant="delete" click="confirmDeleteValor({{ $valorItem['id'] }})"/>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
        </div>
        @endif

        <x-panel-toggle wire:loading.attr="disabled"
            :open="$formularioVisible"
            wire:model="formularioVisible"
        />

        <livewire:admin.tabla-atributos
            :per-page="$perPage"
            key="'tabla-atributo-' . time()"
            lazy
        />


    </div>

    {{-- Modales de ConfirmaciÃ³n --}}
    @if($atributoIdToDelete)
    <x-confirmation-modal
        wire:model="confirmingAtributoDeletion"
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
            Â¿EstÃ¡ seguro de eliminar este atributo?
        </x-slot>

        <x-slot name="footer">
            <div class="flex justify-end gap-2">
                <button wire:click="cancelDelete" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancelar</button>
                <button wire:click="delete" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Eliminar</button>
            </div>
        </x-slot>
    </x-confirmation-modal>
    @endif

    @if($valorIdToDelete)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-96">
            <h3 class="text-lg font-semibold mb-4">Confirmar eliminaciÃ³n</h3>
            <p class="mb-6">Â¿EstÃ¡ seguro de eliminar este valor?</p>
            <div class="flex justify-end gap-2">
                <button wire:click="$set('valorIdToDelete', null)" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Cancelar</button>
                <button wire:click="deleteValor({{ $valorIdToDelete }})" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Eliminar</button>
            </div>
        </div>
    </div>
    @endif

</div>

