<div class="panel-listado bg-white-1 overflow-x-auto">
    {{-- FILTROS SUPERIORES --}}
    <div class="flex flex-wrap gap-4 p-4 border-b border-slate-100 bg-slate-50/50">
        <div class="flex-1 min-w-[200px]">
            <x-input wire:model.live="search" placeholder="Buscar por nombre, email o móvil..." class="w-full text-sm" />
        </div>
        <div class="w-48">
            <x-select wire:model.live="roleFilter" class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500">
                <option value="">Todos los Roles</option>
                @foreach($roles as $role)
                    <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                @endforeach
            </x-select>
        </div>
    </div>

    <table class="w-full table-auto min-w-[600px]">
        <thead>
            <tr>
                <th class="p-4 border-b border-slate-300 text-left text-sm font-normal text-slate-500">Nombre / Móvil</th>
                <th class="p-4 border-b border-slate-300 text-left text-sm font-normal text-slate-500">Email</th>
                <th class="p-4 border-b border-slate-300 text-left text-sm font-normal text-slate-500">Rol</th>
                <th class="p-4 border-b border-slate-300"></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($usuarios as $usuario)
            <tr class="hover:bg-slate-50">
                <td class="p-4 border-b border-slate-200">
                    <div class="flex flex-col">
                        <span class="text-sm font-medium text-slate-800">{{ $usuario->name }}</span>
                        <span class="text-xs text-slate-500">{{ $usuario->movil ?? 'Sin móvil' }}</span>
                    </div>
                </td>
                <td class="p-4 border-b border-slate-200 text-sm text-slate-800">
                    {{ $usuario->email }}
                </td>
                <td class="p-4 border-b border-slate-200">
                    @foreach($usuario->roles as $role)
                        <span class="px-2 py-0.5 text-[10px] uppercase font-bold rounded-full bg-indigo-100 text-indigo-700">
                            {{ $role->name }}
                        </span>
                    @endforeach
                </td>
                <td class="p-4 border-b border-slate-200 text-right">
                    <div class="flex items-center justify-end gap-2">
                        <x-icon-button variant="edit" click="edit({{ $usuario->id }})"/>
                        @if(auth()->id() !== $usuario->id)
                            <x-icon-button variant="delete" click="confirmDelete({{ $usuario->id }})"/>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @if($usuarios->hasPages())
    <div class="mt-4">
        <x-ui.livewire.pagination :paginator="$usuarios" :window="2" :show-summary="true">
            <x-slot:middle>
                <x-ui.livewire.per-page wire:model.live="perPage" class="hidden sm:inline-flex" />
            </x-slot:middle>
        </x-ui.livewire.pagination>
    </div>
    @endif
</div>

