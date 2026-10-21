{{-- resources/views/components/ui/table --}}
@php
$alignmentClasses = [
    'left' => [ 'th' => 'text-left', 'flex' => 'justify-start', ],
    'center' => [ 'th' => 'text-center', 'flex' => 'justify-center', ],
    'right' => [ 'th' => 'text-right', 'flex' => 'justify-end', ], ];
@endphp
<div class="space-y-6">

    {{-- SEARCH --}}
    <x-ui.table.search
        wire:model.live.debounce.300ms="search"
        placeholder="Buscar..."
    />

    {{-- TABLE --}}
    <x-ui.table.table
        :sortable="$sortable"
        :sortableMethod="$sortableMethod"
        :sortableOptions="$sortableOptions"
    >

        {{-- HEADER --}}
        <x-slot name="head">

            <tr>

                {{-- Drag handle header --}}
                @if($sortable)
                    <x-ui.table.th class="w-12">
                    </x-ui.table.th>
                @endif

                @foreach($columns as $column)
                    @php $alignment = $alignmentClasses[$column->alignment()] ?? $alignmentClasses['left']; @endphp
                    <x-ui.table.th
                        wire:click="sortBy('{{ $column->field }}')"
                        class="cursor-pointer {{ $alignment['th'] }}"
                    >

                        <div class="flex items-center gap-1 {{ $alignment['flex'] }}">
                            <span>
                                {{ $column->label }}
                            </span>

                            {{--  @if($column->field === 'activa' || $column->field === 'activo')  --}}
                            {{-- @if($column->extra)
                                <span
                                    x-data="{ tooltip: false }"
                                    @mouseenter="tooltip = true"
                                    @mouseleave="tooltip = false"
                                    class="ui-tooltip text-orange-500 text-sm"
                                    wire:click.stop
                                >
                                    <i class="bi bi-info-circle"></i>

                                    <span
                                        x-show="tooltip"
                                        x-transition
                                        class="ui-tooltip-content"
                                    >
                                        {{ $column->extra_msj }}
                                    </span>
                                </span>
                            @endif --}}
                            @if($column->extra)
                                <span
                                    class="ui-tooltip group relative text-orange-500 text-sm"
                                > 
                                {{--
                                <span
                                    class="ui-tooltip group relative text-orange-500 text-sm"
                                    wire:click.stop
                                > 
                                --}}
                                    <i class="bi bi-info-circle"></i>

                                    <span
                                        class="ui-tooltip-content hidden group-hover:block"
                                    >
                                        {{ $column->extra_msj }}
                                    </span>
                                </span>
                            @endif

                            @if($sortField === $column->field)
                                <span class="text-xs text-indigo-500">
                                    {{ $sortDirection === 'asc'
                                        ? '↑'
                                        : '↓'
                                    }}
                                </span>
                            @endif
                        </div>

                    </x-ui.table.th>

                @endforeach

            </tr>

        </x-slot>

        {{-- BODY --}}
        @forelse($rows as $row)

            <tr
                wire:key="row-{{ $row->id }}"
                @if($sortable)
                    wire:sortable.item="{{ $row->id }}"
                @endif
                class="hover:bg-gray-50 transition"
            >

                {{-- Drag handle --}}
                @if($sortable)
                    <x-ui.table.td>
                        <div
                            wire:sortable.handle
                            class="cursor-move px-2 text-gray-400 flex items-center justify-center"
                            title="Arrastrar para reordenar"
                        >
                            <i class="bi bi-grip-vertical"></i>
                        </div>
                    </x-ui.table.td>
                @endif
                @foreach($columns as $column)

                    <x-ui.table.td>

                        {!! $column->render($row) !!}

                    </x-ui.table.td>

                @endforeach

            </tr>

        @empty

            <x-ui.table.empty
                :colspan="count($columns)"
            />

        @endforelse

        @if(
            (isset($totals) && !empty($totals) && array_sum($totals) > 0)
            ||
            (isset($totals_local_periodo) && !empty($totals_local_periodo) && array_sum($totals_local_periodo) > 0)
        )
        @php
            $mostrar_total_local_periodo = $totals != $totals_local_periodo;
        @endphp
            <tfoot class="bg-slate-50 border-t border-slate-300 font-bold text-slate-800">
                <tr>
                    @php
                        $colspan_sin_filtrar = 0;
                        $proccess_cantidad = false;
                        $proccess_importe = false;
                    @endphp
                    @foreach($columns as $column)
                        @php
                            $fieldName = is_object($column)
                                ? ($column->field ?? null)
                                : ($column['field'] ?? null);
                        @endphp

                        <td class="px-4 py-3 text-sm">

                            @if($fieldName === 'cantidad')
                                @php
                                    $colspan_sin_filtrar++;
                                @endphp

                                <span class="text-xs text-slate-400 block font-normal">
                                    Total:
                                </span>

                                {{ $totals['cantidad'] }}

                            @elseif($fieldName === 'importe')
                                @php
                                    $colspan_sin_filtrar++;
                                    $proccess_importe = true;
                                @endphp

                                <div class="space-y-1">

                                    <div>
                                        <span class="text-xs text-slate-400 font-normal">
                                            Total CUP 
                                            @if(isset($data_decorate) && !empty($data_decorate))
                                            <span
                                                class="text-xs font-semibold px-2 py-0.5 rounded-full ml-2 flex-shrink-0
                                                {{ $data_decorate['decorate_base']['color_text'] }}
                                                {{ $data_decorate['decorate_base']['color_bg'] }}
                                                ">
                                                {{ $data_decorate['decorate_base']['simbolo'] }}
                                            </span>
                                            @endif
                                            :
                                        </span>

                                        <span class="ml-2">
                                            {{ $totals['base'] }}
                                        </span>
                                    </div>

                                    <div>
                                        <span class="text-xs text-slate-400 font-normal">
                                            Total USD 
                                            @if(isset($data_decorate) && !empty($data_decorate))
                                            <span
                                                class="text-xs font-semibold px-2 py-0.5 rounded-full ml-2 flex-shrink-0
                                                {{ $data_decorate['decorate_usd']['color_text'] }}
                                                {{ $data_decorate['decorate_usd']['color_bg'] }}
                                                ">
                                                {{ $data_decorate['decorate_usd']['simbolo'] }}
                                            </span>
                                            @endif
                                            :
                                        </span>

                                        <span class="ml-2">
                                            {{ $totals['usd'] }}
                                        </span>
                                    </div>

                                </div>

                            @else

                                @php
                                    if(!$proccess_importe)
                                        $colspan_sin_filtrar++;
                                @endphp

                            @endif

                        </td>
                    @endforeach
                </tr>
                @if($mostrar_total_local_periodo)
                <tr>
                    @php
                        $colspan_sin_filtrar = 0;
                        $proccess_cantidad = false;
                        $proccess_importe = false;
                    @endphp
                    @foreach($columns as $column)
                        @php
                            $fieldName = is_object($column)
                                ? ($column->field ?? null)
                                : ($column['field'] ?? null);
                        @endphp
                        
                        @php
                            if($fieldName === 'importe')
                                $proccess_importe = true;
                        @endphp
                        
                        @if(!$proccess_cantidad || $proccess_importe)
                        <td>&nbsp;</td>
                        @elseif($proccess_cantidad && !$proccess_importe)
                        <td colspan="{{ $colspan_sin_filtrar }}" class="pt-3">
                            <div class="flex items-center justify-center">
                                <span class="text-xs text-slate-600 font-semibold block">
                                    SIN FILTRAR
                                </span>
                            </div>
                        </td>
                        @endif

                        @php
                            if($fieldName === 'cantidad')
                                $proccess_cantidad = true;
                        @endphp

                    @endforeach
                </tr>
                <tr>
                    @foreach($columns as $column)
                        @php
                            $fieldName = is_object($column)
                                ? ($column->field ?? null)
                                : ($column['field'] ?? null);
                        @endphp

                        <td class="px-4 py-3 text-sm">

                            @if($fieldName === 'cantidad')

                                <span class="text-xs text-slate-400 block font-normal">
                                    Total:
                                </span>

                                {{ $totals_local_periodo['cantidad'] }}

                            @elseif($fieldName === 'importe')

                                <div class="space-y-1">

                                    <div>
                                        <span class="text-xs text-slate-400 font-normal">
                                            Total CUP 
                                            @if(isset($data_decorate) && !empty($data_decorate))
                                            <span
                                                class="text-xs font-semibold px-2 py-0.5 rounded-full ml-2 flex-shrink-0
                                                {{ $data_decorate['decorate_base']['color_text'] }}
                                                {{ $data_decorate['decorate_base']['color_bg'] }}
                                                ">
                                                {{ $data_decorate['decorate_base']['simbolo'] }}
                                            </span>
                                            @endif
                                            :
                                        </span>

                                        <span class="ml-2">
                                            {{ $totals_local_periodo['base'] }}
                                        </span>
                                    </div>

                                    <div>
                                        <span class="text-xs text-slate-400 font-normal">
                                            Total USD 
                                            @if(isset($data_decorate) && !empty($data_decorate))
                                            <span
                                                class="text-xs font-semibold px-2 py-0.5 rounded-full ml-2 flex-shrink-0
                                                {{ $data_decorate['decorate_usd']['color_text'] }}
                                                {{ $data_decorate['decorate_usd']['color_bg'] }}
                                                ">
                                                {{ $data_decorate['decorate_usd']['simbolo'] }}
                                            </span>
                                            @endif
                                            :
                                        </span>

                                        <span class="ml-2">
                                            {{ $totals_local_periodo['usd'] }}
                                        </span>
                                    </div>

                                </div>

                            @endif

                        </td>
                    @endforeach
                </tr>
                @endif
            </tfoot>
        @endif

    </x-ui.table.table>

    {{-- PAGINATION --}}
    <x-ui.table.pagination
        :rows="$rows"
    />

</div>