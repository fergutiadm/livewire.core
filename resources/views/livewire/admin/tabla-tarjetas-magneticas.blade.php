<div class="panel-listado bg-white-1 overflow-x-auto">
    <table class="w-full table-auto min-w-[600px]">
        <thead>
            <tr>
                <th class="p-4 border-b border-slate-300 bg-slate-50 text-left">
                    <p class="block text-sm font-normal leading-none text-slate-500">
                    Número
                    </p>
                </th>
                <th class="p-4 border-b border-slate-300 bg-slate-50 text-left">
                    <p class="block text-sm font-normal leading-none text-slate-500">
                    Propietario
                    </p>
                </th>
                <th class="p-4 border-b border-slate-300 bg-slate- text-left">
                    <p class="block text-sm font-normal leading-none text-slate-500"></p>
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($tarjetas as $tarjeta)
            {{--  <tr class="hover:bg-slate-50 {{ $tarjeta->es_principal ? 'bg-sky-200 font-semibold' : '' }}">  --}}
            <tr class="hover:bg-slate-50">
                <td class="p-4 border-b border-slate-200">
                    <p class="block text-sm text-slate-800 flex items-center flex-wrap-0">
                        <span class="truncate">{{ $tarjeta->numero }}</span>

                        <span
                            @class([
                                'text-xs font-semibold px-2 py-0.5 rounded-full ml-2 flex-shrink-0',
                                $tarjeta->color_text ?? 'text-white',
                                $tarjeta->color_bg ?? 'bg-indigo-500',
                            ])>
                            {{ $tarjeta->monedaCodigo }}
                        </span>
                    </p>

                </td>
                <td class="p-4 border-b border-slate-200">
                    <p class="block text-sm text-slate-800">
                    {{ $tarjeta->propietario }}
                    </p>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @if($tarjetas->hasPages())
    <div class="mt-4">
        <x-ui.livewire.pagination :paginator="$tarjetas" :window="2" :show-summary="true">
            <x-slot:middle>
                <x-ui.livewire.per-page wire:model="perPage" class="hidden sm:inline-flex" />
            </x-slot:middle>
        </x-ui.livewire.pagination>
    </div>
    @endif
</div>
