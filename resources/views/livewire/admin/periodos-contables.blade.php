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
            {{-- =========================
             | FORMULARIO (DERECHA)
             ========================= --}}
            <form wire:submit.prevent="save">
                <div class="panel-form-grid-4">
                    {{-- Local --}}
                    <div class="panel-form-group">
                        <x-label class="panel-form-label">Local</x-label>
                        <x-select wire:model="localId" wire:change="localChanged" class="panel-form-input">
                            @foreach($locales as $local)
                                <option value="{{ $local->id }}">{{ $local->nombre }}</option>
                            @endforeach
                        </x-select>
                    </div>

                    {{-- Nombre --}}
                    <div class="panel-form-group">
                        <x-label class="panel-form-label">Nombre (opcional)</x-label>
                        <x-input-clear
                            wire:model.defer="nombre"
                            placeholder="Nombre"
                            :disabled="$cerrado"
                            style="slide"
                        />
                    </div>

                    {{-- Flags --}}
                    <div class="panel-form-group flex items-center gap-4">
                        <x-label class="flex items-center gap-2 text-sm">
                            <x-checkbox type="checkbox" wire:model="activo"/>
                            <span>Activo</span>
                        </x-label>

                        <x-label class="flex items-center gap-2 text-sm">
                            <x-checkbox type="checkbox" wire:model="cerrado"/>
                            <span>Cerrado</span>
                        </x-label>
                    </div>
                </div>
                <div class="panel-form-grid-3">

                    {{-- Fecha inicio --}}
                    <x-input-flatpickr
                        wire:key="fecha-inicio-{{ $periodoId ?? 'new' }}"
                        label="Fecha Inicio"
                        wire:model.defer="fechaInicio"
                        placeholder="dd-mm-aaaa"
                    />

                    {{-- Fecha fin --}}
                    <x-input-flatpickr
                        wire:key="fecha-fin-{{ $periodoId ?? 'new' }}"
                        label="Fecha Fin"
                        wire:model.defer="fechaFin"
                        placeholder="dd-mm-aaaa"
                    />
                </div>

                {{-- Acciones --}}
                <div class="panel-form-actions">
                    <x-button class="btn-primary-2 disabled:opacity-25" wire:click="$set(disabledBtnSave, true)">
                        {{ $periodoId ? 'Salvar' : 'Crear' }}
                    </x-button>

                    @if($periodoId)
                        <button
                            type="button"
                            wire:click="cancel"
                            class="btn-secondary-2">
                            Cancelar
                        </button>
                    @endif
                </div>
            </form>
        </div>
        @endif

        <x-panel-toggle wire:loading.attr="disabled"
            :open="$formularioVisible"
            wire:model="formularioVisible"
        />

        <livewire:admin.tabla-periodos-contables
            :per-page="$perPage"
            key="'tabla-periodo-contable-' . time()"
            lazy
        />


    </div>

    {{-- =========================
     | MODAL ELIMINAR
     ========================= --}}
    @if($periodoIdToDelete)
        <x-confirmation-modal
        wire:model="confirmingPeriodoDeletion"
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
            Â¿EstÃ¡ seguro de eliminar este periodo contable?
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

