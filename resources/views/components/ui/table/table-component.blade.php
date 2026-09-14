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
                            @if($column->extra)
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

    </x-ui.table.table>

    {{-- PAGINATION --}}
    <x-ui.table.pagination
        :rows="$rows"
    />

</div>