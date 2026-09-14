<div class="panel-listado bg-white-1 overflow-x-auto">

    {{-- Lista Drag & Drop de Atributos --}}
    <div class="overflow-x-auto shadow rounded-lg">
        {{-- Header tipo tabla --}}
        <div class="flex font-semibold text-slate-600 border-b border-slate-300 p-2">
            <div class="w-8"></div> {{-- espacio para handle --}}
            <div class="flex-1">Nombre</div>
            <div class="flex-1">DescripciÃ³n</div>
            <div class="w-48 text-right">Acciones</div>
        </div>

        {{-- Items --}}
        <ul wire:sortable="reorderAtributos" wire:sortable.options="{ animation: 150 }">
            @foreach($atributos as $atributo)
            <li wire:sortable.item="{{ $atributo->id }}" wire:key="atributo-{{ $atributo->id }}"
                class="flex items-center border-b border-slate-200 hover:bg-slate-50 p-2">

                {{-- Handle --}}
                <div wire:sortable.handle class="w-8 cursor-move text-gray-500 flex-none text-center">
                    <i class="bi bi-grip-vertical"></i>
                </div>

                {{-- Nombre + orden --}}
                <div class="flex-1 flex items-center gap-2">
                    <span class="truncate text-slate-700">{{ $atributo->nombre }}</span>
                    <span class="text-xs font-semibold text-slate-100 bg-indigo-500 px-2 py-0.5 rounded-full">{{ $atributo->orden_visual }}</span>
                </div>

                {{-- DescripciÃ³n --}}
                <div class="flex-1 text-slate-700">{{ $atributo->descripcion }}</div>

                {{-- Acciones --}}
                <div class="w-48 flex justify-end gap-2">
                    <button wire:click="edit({{ $atributo->id }})" class="btn-primary btn-icon" title="Editar">
                        <i class="bi bi-pencil-fill"></i>
                    </button>
                    <button wire:click="confirmDelete({{ $atributo->id }})" class="btn-danger btn-icon" title="Eliminar">
                        <i class="bi bi-trash-fill"></i>
                    </button>
                </div>
            </li>
            @endforeach
        </ul>
    </div>

    {{-- PaginaciÃ³n --}}
    @if($atributos->hasPages())
    <div class="mt-4">
        <x-ui.livewire.pagination :paginator="$atributos" :window="2" :show-summary="true">
            <x-slot:middle>
                <x-ui.livewire.per-page wire:model="perPage" class="hidden sm:inline-flex" />
            </x-slot:middle>
        </x-ui.livewire.pagination>
    </div>
    @endif

</div>

