<div class="panel-listado bg-white-1 overflow-x-auto">
    <div class="overflow-x-auto">
        <ul wire:sortable="reorderCategorias" wire:sortable.options="{ animation: 150 }" class="w-full">
            {{-- Encabezado --}}
            <li class="flex bg-slate-100 text-slate-600 p-2 font-bold">
                <div class="flex-1">Nombre</div>
                <div class="flex-1">Descripción</div>
                <div class="flex-1">% Descuento</div>
                <div class="flex-none"></div>
            </li>

            @forelse($categorias as $categoria)
                @php
                    $icono = $categoria->icono ?? '';
                    $icono_secundario = $categoria->icono_secundario ?? '';
                    $class_colores = $categoria->color_bg ?? '';
                    $class_colores .= ' ' . $categoria->color_text ?? '';
                    $class_rounded_color = $categoria->color_bg ? 'rounded-sm border-slate-500' : 'rounded-sm border-slate-400';
                @endphp
                <li wire:sortable.item="{{ $categoria->id }}" wire:key="categoria-{{ $categoria->id }}"
                    class="flex items-center gap-2 border-b border-slate-300 hover:bg-slate-50 listado-data-li">
                    {{-- Drag handle --}}
                    <div wire:sortable.handle class="cursor-move px-2 text-gray-400 flex-none">
                        <i class="bi bi-grip-vertical"></i>
                    </div>

                    <div class="flex-1 flex items-center gap-2">
                        <span class="text-xs font-semibold text-white bg-indigo-500 px-2 py-0.5 rounded-full">{{ $categoria->orden_visual }}</span>
                        <span class="truncate">{{ $categoria->nombre }} </span>
                        @if($icono)
                            <span class="text-xs {{ $class_colores }} px-2 py-0.5 rounded-full">
                                <i class="bi {{ $icono }}"></i>
                            </span>
                        @endif
                        @if($icono_secundario)
                            <span class="text-xs {{ $class_colores }} px-2 py-0.5 rounded-full">
                                <i class="bi {{ $icono_secundario }}"></i>
                            </span>
                        @endif
                    </div>
                    <div class="flex-1">{{ $categoria->descripcion }}</div>
                    <div class="flex-1">{{ $categoria->porciento_descuento }}</div>

                    <div class="flex gap-2 flex-none">
                        <x-icon-button variant="edit" click="edit({{ $categoria->id }})"/>
                        <x-icon-button variant="delete" click="confirmDelete({{ $categoria->id }})"/>
                        <x-icon-button variant="atributos" click="editAtributos({{ $categoria->id }})"/>
                    </div>

                </li>

            @empty
                <li class="flex items-center justify-center p-10 text-center italic text-slate-500">
                    Nada para mostrar
                </li>
            @endforelse
        </ul>

        {{-- Paginación --}}
        @if($categorias->hasPages())
            <div class="mt-4">
                <x-ui.livewire.pagination :paginator="$categorias" :window="2" :show-summary="true" />
            </div>
        @endif
    </div>
</div>
