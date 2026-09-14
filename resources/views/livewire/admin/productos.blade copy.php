@php
    $encabezados = ['Nombre', 'Categoría', 'Costo', 'Precio', '% Descuento'];
@endphp

<div>
    <div class="panel-layout-1">
        {{-- Tabla (80%) --}}
        <div class="panel-left bg-white-1 overflow-x-auto">
            <x-table>
                <table class="w-full table-auto min-w-[700px]">
                    <thead>
                        <tr>
                            <th class="p-4 border-b border-slate-300 bg-slate-50 text-left">Nombre</th>
                            <th class="p-4 border-b border-slate-300 bg-slate-50 text-left">Categoría</th>
                            <th class="p-4 border-b border-slate-300 bg-slate-50 text-left">Costo</th>
                            <th class="p-4 border-b border-slate-300 bg-slate-50 text-left">Precio</th>
                            <th class="p-4 border-b border-slate-300 bg-slate-50 text-left">% Descuento</th>
                            <th class="p-4 border-b border-slate-300 bg-slate-50"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($productos as $producto)
                        <tr class="hover:bg-slate-50" wire:key="producto-{{ $producto->id }}">
                            <td class="p-4 border-b border-slate-200">{{ $producto->nombre }}</td>
                            <td class="p-4 border-b border-slate-200">{{ $producto->categoria?->nombre }}</td>
                            <td class="p-4 border-b border-slate-200">{{ number_format($producto->costo, 2) }}</td>
                            <td class="p-4 border-b border-slate-200">{{ number_format($producto->precio, 2) }}</td>
                            <td class="p-4 border-b border-slate-200">{{ $producto->porciento_descuento }}%</td>
                            <td class="p-4 border-b border-slate-200">
                                <div class="flex items-center gap-2">
                                    <button wire:click="edit({{ $producto->id }})" type="button" class="btn-primary-1 btn-icon" title="Editar">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                    <button wire:click="confirmDelete({{ $producto->id }})" type="button" class="btn-danger-1 btn-icon" title="Eliminar">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                @if($productos->hasPages())
                <div class="mt-4">
                    <x-ui.livewire.pagination :paginator="$productos" :window="2" :show-summary="true">
                        <x-slot:middle>
                            <x-ui.livewire.per-page wire:model="perPage" class="hidden sm:inline-flex" />
                        </x-slot:middle>
                    </x-ui.livewire.pagination>
                </div>
                @endif
            </x-table>
        </div>

        {{-- Formulario (20%) --}}
        <div class="panel-right panel-form">
            <form wire:submit.prevent="save">
                <div class="panel-form-group">
                    <x-label class="panel-form-label">Local</x-label>
                    <select wire:model="localId" wire:change="localChanged" class="panel-form-input">
                        <option value="">Todos</option>
                        @foreach($locales as $local)
                            <option value="{{ $local->id }}">{{ $local->nombre }}</option>
                        @endforeach
                    </select>
                    @error('localId')<p class="panel-form-error">{{ $message }}</p>@enderror
                </div>

                <div class="panel-form-group">
                    <x-label class="panel-form-label">Categoría</x-label>
                    <select wire:model="categoriaId" wire:change="categoriaChanged" class="panel-form-input">
                        <option value="">Seleccione</option>
                        @foreach($categorias as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                        @endforeach
                    </select>
                    @error('categoriaId')<p class="panel-form-error">{{ $message }}</p>@enderror
                </div>

                <div class="panel-form-group">
                    <x-label class="panel-form-label">Nombre</x-label>
                    <x-input class="panel-form-input" wire:model="nombre"/>
                    @error('nombre')<p class="panel-form-error">{{ $message }}</p>@enderror
                </div>

                <div class="panel-form-group">
                    <x-label class="panel-form-label">Moneda</x-label>
                    <select wire:model="monedaId" class="panel-form-input">
                        <option value="">Seleccione</option>
                        @foreach($monedas as $moneda)
                            <option value="{{ $moneda->id }}">{{ $moneda->codigo }} ({{ $moneda->simbolo }})</option>
                        @endforeach
                    </select>
                    @error('monedaId')<p class="panel-form-error">{{ $message }}</p>@enderror
                </div>

                <div class="panel-form-group">
                    <x-label class="panel-form-label">Imágenes</x-label>
                    <input type="file" wire:model="imagenes" multiple class="panel-form-input" accept="image/*">
                    @error('imagenes.*')<p class="panel-form-error">{{ $message }}</p>@enderror

                    {{-- Previsualización --}}
                    @if($imagenes)
                        <div class="flex gap-2 mt-2 flex-wrap">
                            @foreach($imagenes as $img)
                                <img src="{{ $img->temporaryUrl() }}" class="h-16 w-16 object-cover rounded">
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="panel-form-group">
                    <x-label class="panel-form-label">Costo</x-label>
                    <x-input type="number" step="0.01" class="panel-form-input" wire:model="costo"/>
                    @error('costo')<p class="panel-form-error">{{ $message }}</p>@enderror
                </div>

                <div class="panel-form-group">
                    <x-label class="panel-form-label">Precio</x-label>
                    <x-input type="number" step="0.01" class="panel-form-input" wire:model="precio"/>
                    @error('precio')<p class="panel-form-error">{{ $message }}</p>@enderror
                </div>

                <div class="panel-form-group">
                    <x-label class="panel-form-label">% Descuento</x-label>
                    <x-input type="number" step="0.01" class="panel-form-input" wire:model="porciento_descuento"/>
                </div>

                <div class="panel-form-group">
                    <x-label class="panel-form-label">Código</x-label>
                    <x-input class="panel-form-input" wire:model="codigo"/>
                    @error('codigo')<p class="panel-form-error">{{ $message }}</p>@enderror
                </div>

                <div class="panel-form-actions">
                    @if($productoId)
                        <x-button class="btn-primary-2">Salvar</x-button>
                        <button type="button" wire:click="cancel" class="btn-cancel-2">Cancelar</button>
                    @else
                        <x-button class="btn-primary-2">Crear</x-button>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Modal confirmación --}}
    @if($productoIdToDelete)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-96">
            <h3 class="text-lg font-semibold mb-4">Confirmar eliminación</h3>
            <p class="mb-6">¿Está seguro de eliminar este producto?</p>
            <div class="flex justify-end gap-2">
                <button wire:click="cancelDelete" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Cancelar</button>
                <button wire:click="delete" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Eliminar</button>
            </div>
        </div>
    </div>
    @endif
</div>
