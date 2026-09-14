<div class="panel-listado bg-white-1 overflow-x-auto">
    {{-- FILTROS SUPERIORES --}}
    <div class="flex flex-wrap gap-4 p-4 border-b border-slate-100 bg-slate-50/50">
        <div class="flex-1 min-w-[200px]">
            <x-input wire:model.live="search" placeholder="Buscar por nombre o descripción..." class="w-full text-sm" />
        </div>
    </div>
    <table class="w-full table-auto min-w-[600px]">
            <thead>
            <tr>
                <th class="p-4 border-b border-slate-300 bg-slate- text-left">
                <p class="block text-sm font-normal leading-none text-slate-500">
                    Nombre
                </p>
                </th>
                <th class="p-4 border-b border-slate-300 bg-slate-50 text-left">
                <p class="block text-sm font-normal leading-none text-slate-500">
                    Descripción
                </p>
                </th>
                <th class="p-4 border-b border-slate-300 bg-slate-50">
                <p class="block text-sm font-normal leading-none text-slate-500"></p>
                </th>
            </tr>
            </thead>
            <tbody>
            @foreach ($locales as $local)
            <tr class="hover:bg-slate-50">
                <td class="p-4 border-b border-slate-200">
                    <p class="block text-sm text-slate-800">
                        {{ $local->nombre }}
                    </p>
                </td>
                <td class="p-4 border-b border-slate-200">
                    <p class="block text-sm text-slate-800">
                        {{ $local->descripcion }}
                    </p>
                </td>
                <td class="p-4 border-b border-slate-200">
                    <div class="flex items-center gap-2">
                        <x-icon-button variant="edit" click="edit({{ $local->id }})"/>
                        <x-icon-button variant="delete" click="confirmDelete({{ $local->id }})"/>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @if($locales->hasPages())
    <div class="mt-4">
        <x-ui.livewire.pagination :paginator="$locales" :window="2" :show-summary="true">
            <x-slot:middle>
                <x-ui.livewire.per-page wire:model="perPage" class="hidden sm:inline-flex" />
            </x-slot:middle>
        </x-ui.livewire.pagination>
    </div>
    @endif
</div>
