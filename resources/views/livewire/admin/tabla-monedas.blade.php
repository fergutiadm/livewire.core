<div class="panel-listado bg-white-1 overflow-x-auto">
    <table class="w-full table-auto min-w-[600px]">
        <thead>
            <tr>
                <th class="p-4 border-b border-slate-300 bg-slate-50 text-left">
                    <p class="block text-sm font-normal leading-none text-slate-500">
                    Código
                    </p>
                </th>
                <th class="p-4 border-b border-slate-300 bg-slate-50 text-left">
                    <p class="block text-sm font-normal leading-none text-slate-500">
                    Nombre
                    </p>
                </th>
                <th class="p-4 border-b border-slate-300 bg-slate-50 text-left">
                    <p class="block text-sm font-normal leading-none text-slate-500">
                    TasaCambio
                    </p>
                </th>
                <th class="p-4 border-b border-slate-300 bg-slate-50 text-left">
                    <p class="block text-sm font-normal leading-none text-slate-500">
                    Activa
                    </p>
                </th>
                <th class="p-4 border-b border-slate-300 bg-slate- text-left">
                    <p class="block text-sm font-normal leading-none text-slate-500"></p>
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($monedas as $moneda)
            <tr class="hover:bg-slate-50 {{ $moneda->es_principal ? 'bg-sky-200 font-semibold' : '' }}">
                <td class="p-4 border-b border-slate-200">

                    <x-moneda-simple-vista :moneda="$moneda"/>

                </td>
                <td class="p-4 border-b border-slate-200">
                    <p class="block text-sm text-slate-800">
                    {{ $moneda->nombre }}
                    </p>
                </td>
                <td class="p-4 border-b border-slate-200">
                    <p class="block text-sm text-slate-800">
                    {{ $moneda->tasa_cambio }}
                    </p>
                </td>
                <td class="p-4 border-b border-slate-200">
                    <p class="block text-sm text-slate-800">
                    {{ $moneda->activa }}
                    </p>
                </td>
                <td class="p-4 border-b border-slate-200">
                    <div class="flex items-center gap-2">
                        <x-icon-button variant="edit" click="edit({{ $moneda->id }})"/>

                        @if(!$moneda->es_principal)
                        <x-icon-button variant="delete" click="confirmDelete({{ $moneda->id }})"/>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @if($monedas->hasPages())
    <div class="mt-4">
        <x-ui.livewire.pagination :paginator="$monedas" :window="2" :show-summary="true">
            <x-slot:middle>
                <x-ui.livewire.per-page wire:model="perPage" class="hidden sm:inline-flex" />
            </x-slot:middle>
        </x-ui.livewire.pagination>
    </div>
    @endif
</div>
