<div class="panel-listado bg-white-1 overflow-x-auto">
    <div class="overflow-x-auto">

        <table class="w-full table-auto min-w-[700px]">
            <thead>
                <tr class="bg-slate-100">
                    <th class="p-3 text-left text-sm text-slate-600">Periodo</th>
                    <th class="p-3 text-left text-sm text-slate-600">Inicio</th>
                    <th class="p-3 text-left text-sm text-slate-600">Fin</th>
                    <th class="p-3 text-left text-sm text-slate-600">Descripción</th>
                    <th class="p-3 text-right text-sm text-slate-600"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($periodos_contables as $periodo)
                    <tr class="border-b hover:bg-slate-50">
                        {{-- Nombre + iconos --}}
                        <td class="p-3">
                            <div class="flex items-center gap-2">

                                {{-- Cerrado --}}
                                @if($periodo->cerrado)
                                    <i class="bi bi-x-circle-fill text-red-500"
                                        title="Periodo cerrado"></i>
                                @endif

                                {{-- Activo --}}
                                @if($periodo->activo)
                                    <i class="bi bi-check-circle-fill text-green-600"
                                        title="Periodo activo"></i>
                                @endif

                                    <span class="text-sm text-slate-800 font-medium">
                                        {{ $periodo->nombre ?: (
                                            $periodo->fecha_inicio->format('d-m-Y')
                                            . ' - ' .
                                            $periodo->fecha_fin->format('d-m-Y')
                                        ) }}
                                    </span>
                            </div>
                        </td>

                        {{-- Fecha inicio --}}
                        <td class="listado-data-td">
                            {{ $periodo->fecha_inicio->format('d-m-Y') }}
                        </td>

                        {{-- Fecha fin --}}
                        <td class="listado-data-td">
                            {{ $periodo->fecha_fin->format('d-m-Y') }}
                        </td>

                        {{-- Descripción --}}
                        <td class="listado-data-td">
                            {{ $periodo->descripcion }}
                        </td>

                        {{-- Acciones --}}
                        <td class="p-3">
                            <div class="flex justify-end gap-2">
                                <x-icon-button variant="edit" disabled="{{ $periodo->cerrado }}" click="edit({{ $periodo->id }})"/>
                                <x-icon-button variant="delete" disabled="{{ $periodo->cerrado }}" click="confirmDelete({{ $periodo->id }})"/>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="p-6 text-center text-slate-500">
                            No existen periodos contables para este local
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Paginación --}}
        @if($periodos_contables->hasPages())
            <div class="mt-4">
                <x-ui.livewire.pagination
                    :paginator="$periodos_contables"
                    :window="2"
                    :show-summary="true"
                >
                    <x-slot:middle>
                        <x-ui.livewire.per-page
                            wire:model="perPage"
                            class="hidden sm:inline-flex"
                        />
                    </x-slot:middle>
                </x-ui.livewire.pagination>
            </div>
        @endif
    </div>
</div>
