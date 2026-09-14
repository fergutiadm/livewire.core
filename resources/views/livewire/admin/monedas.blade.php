@php
    $encabezados = ['Nombre', 'Descripción'];
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

            {{-- Datos principales --}}
            <div class="grid grid-cols-1 md:grid-cols-6 gap-4">

                {{-- Código --}}
                <div class="panel-form-group md:col-span-1">
                    <x-label class="panel-form-label">Código</x-label>

                    <x-input
                        class="panel-form-input"
                        wire:model="codigo"
                    />

                    @error('codigo')
                        <p class="panel-form-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nombre --}}
                <div class="panel-form-group md:col-span-2">
                    <x-label class="panel-form-label">Nombre</x-label>

                    <x-input
                        class="panel-form-input"
                        wire:model="nombre"
                    />

                    @error('nombre')
                        <p class="panel-form-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Símbolo --}}
                <div class="panel-form-group md:col-span-1">
                    <x-label class="panel-form-label">Símbolo</x-label>

                    <x-input
                        class="panel-form-input"
                        wire:model="simbolo"
                    />

                    @error('simbolo')
                        <p class="panel-form-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tasa de Cambio --}}
                <div class="panel-form-group md:col-span-1">
                    <x-label class="panel-form-label">Tasa de Cambio</x-label>

                    <x-input
                        class="panel-form-input"
                        wire:model="tasa_cambio"
                    />

                    @error('tasa_cambio')
                        <p class="panel-form-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Principal --}}
                <div class="panel-form-group md:col-span-1 flex items-center gap-3">
                    <x-label class="panel-form-label">Principal</x-label>

                    <x-checkbox
                        wire:model="es_principal"
                        class="checkbox-1"
                    />

                    @error('principal')
                        <p class="panel-form-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Activa --}}
                <div class="panel-form-group md:col-span-1 flex items-center gap-3">
                    <x-label class="panel-form-label">Activa</x-label>

                    <x-checkbox
                        wire:model="activa"
                        class="checkbox-1"
                    />

                    @error('activa')
                        <p class="panel-form-error">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            {{-- Paleta de colores --}}
            <div class="panel-form-group mt-4">

                <x-label class="panel-form-label">
                    Paleta de Color
                </x-label>

                <div class="flex flex-wrap gap-2 mt-2">

                    @foreach($palette as $p)

                        <button
                            type="button"
                            wire:click="selectPalette('{{ $p['bg'] }}', '{{ $p['text'] }}')"
                            class="w-9 h-9 rounded-full border-2 flex items-center justify-center
                                {{ $p['bg'] }}
                                {{ $color_bg === $p['bg'] && $color_text === $p['text']
                                    ? 'border-black ring-2 ring-offset-2 ring-black'
                                    : 'border-gray-300'
                                }}"
                        >
                            <span class="text-xs font-bold {{ $p['text'] }}">
                                Aa
                            </span>
                        </button>

                    @endforeach

                </div>

                @error('color_bg')
                    <p class="panel-form-error">{{ $message }}</p>
                @enderror

            </div>

            {{-- Botones --}}
            <div class="panel-form-actions flex items-center gap-3 mt-6">

                @if($monedaId)

                    <x-button class="btn-primary-2">
                        Salvar
                    </x-button>

                    <button
                        type="button"
                        wire:click="cancel"
                        class="btn-cancel-2"
                    >
                        Cancelar
                    </button>

                @else

                    <x-button class="btn-primary-2">
                        Crear
                    </x-button>

                @endif

            </div>

        </form>
        </div>
        @endif

        <x-panel-toggle wire:loading.attr="disabled"
            :open="$formularioVisible"
            wire:model="formularioVisible"
        />

        <livewire:admin.tabla-monedas
            :per-page="$perPage"
            {{--  key="'tabla-moneda-' . time()"  --}}
            key="tabla-moneda"
            {{--  lazy  --}}
        />



    </div>
    {{-- Modal de confirmación --}}
    @if($monedaIdToDelete)
    <x-confirmation-modal
        wire:model="confirmingMonedaDeletion"
        onCancel="cancelDelete"
        onConfirm="delete"
        maxWidth="md"
    >
        {{-- Título personalizado para el slot title --}}
        <x-slot name="title">
            Confirmar eliminación
        </x-slot>

        {{-- Contenido del mensaje para el slot por defecto --}}
        <x-slot name="content">
            ¿Está seguro de eliminar esta moneda?
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
