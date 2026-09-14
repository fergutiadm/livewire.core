<div class="space-y-4 p-4">

    {{-- Filtros --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">

        {{-- Buscar --}}
        <div>
            <x-input
                type="text"
                wire:model.live.debounce.300ms="search"
                {{--  wire:change="filtrar"  --}}
                placeholder="Buscar operación..."
                class="panel-form-input w-full"
            />
        </div>

        {{-- Modelo --}}
        <div>
            <x-select
                wire:model.live="modelFilter"
                {{--  wire:change="filtrar"  --}}
                class="panel-form-input w-full"
            >
                <option value="">Todos los modelos</option>

                @foreach ($models as $type)
                    <option value="{{ $type }}">
                        {{ class_basename($type) }}
                    </option>
                @endforeach
            </x-select>
        </div>

        {{-- Usuario --}}
        <div>
            <x-select
                wire:model.live="userFilter"
                {{--  wire:change="filtrar"  --}}
                class="panel-form-input w-full"
            >
                <option value="">Todos los usuarios</option>

                @foreach ($users as $user)
                    <option value="{{ $user->id }}">
                        {{ $user->name }}
                    </option>
                @endforeach
            </x-select>
        </div>

        {{-- Fecha desde --}}
        <div>
            <x-input-flatpickr
                wire:model="dateFrom"
                placeholder="Desde dd-mm-aaaa"
            />
        </div>

        {{-- Fecha hasta --}}
        <div>
            <x-input-flatpickr
                wire:model="dateTo"
                placeholder="Hasta dd-mm-aaaa"
            />
        </div>


    </div>

    {{-- Tabla de trazas --}}
    <div class="overflow-x-auto bg-white shadow rounded-lg">

        <table class="table-base w-full">

            <thead class="table-header">
                <tr>
                    <th class="p-4">Fecha</th>
                    <th class="p-4">Usuario</th>
                    <th class="p-4">Modelo</th>
                    <th class="p-4">Operación</th>
                    <th class="p-4">Detalles</th>
                </tr>
            </thead>

            <tbody>

                @php
                    $operationColors = [
                        'crear'      => 'bg-green-100 text-green-800',
                        'actualizar' => 'bg-blue-100 text-blue-800',
                        'eliminar'   => 'bg-red-100 text-red-800',
                        'restaurar'  => 'bg-yellow-100 text-yellow-800',
                    ];
                @endphp

                @forelse ($trazas as $traza)

                    <tr class="table-row align-top">

                        <td class="table-cell">
                            {{ $traza->created_at->format('Y-m-d H:i') }}
                        </td>

                        <td class="table-cell">
                            {{ $traza->user?->name ?? 'Sistema' }}
                        </td>

                        <td class="table-cell">
                            {{ class_basename($traza->trazable_type) }}
                        </td>

                        <td class="table-cell">

                            <span class="px-2 py-1 rounded text-sm font-semibold {{ $operationColors[$traza->operacion] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($traza->operacion) }}
                            </span>

                        </td>

                        <td class="table-cell">

                            <details class="text-xs">

                                <summary class="cursor-pointer">
                                    Ver diff
                                </summary>

                                <pre class="mt-1 bg-gray-50 p-2 rounded overflow-x-auto">{{ json_encode($traza->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>

                            </details>

                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="5" class="text-center py-4">
                            Sin resultados
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>
    {{--  <livewire:admin.trazas.tabla
        :search="$search"
        :model-filter="$modelFilter"
        :user-filter="$userFilter"
        :date-from="$dateFrom"
        :date-to="$dateTo"
    />  --}}


