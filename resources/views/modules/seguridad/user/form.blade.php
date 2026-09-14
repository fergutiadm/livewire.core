<div
    class="
        rounded-2xl
        border
        border-slate-200
        bg-white
        p-6
        shadow-sm
    "
>

    <form novalidate
        wire:submit.prevent="save"
        x-on:submit="$dispatch('loading-start', { message: 'Guardando usuario...' })"
        class="space-y-6"
    >

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                {{-- NAME --}}
                <div>

                    <label class="block text-sm font-medium text-slate-700 mb-1">
                        Nombre
                    </label>

                    <input
                        type="text"
                        wire:model="name"
                        class="w-full rounded-xl border-slate-300"
                        placeholder="Nombre del usuario"
                    >
                    @error('name')<p class="panel-form-error">{{ $message }}</p>@enderror
                </div>

                {{-- EMAIL --}}
                <div
                x-data="{ email: @js($email) }"
                >
                    <label class="block text-sm font-medium text-slate-700 mb-1">
                        Email
                    </label>

                    <div class="flex">
                        <input
                            type="text"
                            wire:model="email"
                            x-model="email"
                            x-ref="email"
                            class="w-full rounded-l-xl border-slate-300 rounded-r-none"
                            placeholder="Correo del usuario"
                        >

                        <button
                            type="button"
                            x-on:click="
                                email = email.trim() + '@';

                                $refs.email.focus();
                                $refs.email.setSelectionRange(
                                    $refs.email.value.length,
                                    $refs.email.value.length
                                );
                            "
                            x-bind:disabled="email.trim() === '' || email.includes('@')"
                            class="px-4 rounded-r-xl border border-l-0 border-slate-300 bg-slate-100 text-slate-600
                                hover:bg-slate-200 disabled:opacity-50 disabled:cursor-not-allowed"
                            title="Agregar @"
                        >
                            @
                        </button>
                    </div>

                    @error('email')
                        <p class="panel-form-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- MOVIL --}}
                <div>

                    <label class="block text-sm font-medium text-slate-700 mb-1">
                        Móvil
                    </label>

                    <input
                        type="text"
                        wire:model="movil"
                        class="w-full rounded-xl border-slate-300"
                        placeholder="Móvil del usuario"
                    >
                    @error('movil')<p class="panel-form-error">{{ $message }}</p>@enderror
                </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

            {{-- ROLES --}}
            <div>
                <x-label value="Rol de Usuario" />
                <x-select wire:model="role" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500">
                    <option value="">Seleccione un rol...</option>
                    @foreach($roles as $r)
                        <option value="{{ $r->name }}">{{ ucfirst($r->name) }}</option>
                    @endforeach
                </x-select>
                <x-input-error for="role" />
            </div>

            {{-- PASSWORD AND CONFIRMATION --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4 p-3 bg-slate-50 rounded-lg border border-slate-200">

                <div class="md:col-span-2">

                    <p class="text-xs text-slate-500 italic">
                        {{ $userId
                            ? 'Deje en blanco si no desea cambiar la contraseña actual.'
                            : 'Asigne una contraseña inicial para el nuevo usuario.'
                        }}
                    </p>

                </div>

                <div>
                    <x-label value="Nueva Contraseña" />

                    <x-input
                        wire:model="password"
                        class="w-full"
                        type="password"
                        placeholder="Ingrese la nueva contraseña"
                        autocomplete="new-password"
                    />
                </div>

                <div>
                    <x-label value="Confirmar Contraseña" />

                    <x-input
                        wire:model="password_confirmation"
                        class="w-full"
                        type="password"
                        placeholder="Repita la nueva contraseña"
                    />
                </div>

                <div class="md:col-span-2">

                    <p class="text-xs">
                        <x-input-error for="password" />
                    </p>

                    <p class="text-xs">
                        <x-input-error for="password_confirmation" />
                    </p>

                </div>

            </div>

        </div>


        {{-- ACTIONS --}}
        <div class="flex items-center gap-3">

            <button
                type="submit"
                class="
                    inline-flex
                    items-center
                    rounded-xl
                    bg-indigo-600
                    px-5
                    py-2
                    text-sm
                    font-medium
                    text-white
                    hover:bg-indigo-700
                "
            >

                Guardar

            </button>

            <button
                type="button"
                wire:click="cancel"
                class="
                    inline-flex
                    items-center
                    rounded-xl
                    border
                    border-slate-300
                    bg-white
                    px-5
                    py-2
                    text-sm
                    font-medium
                    text-slate-700
                    hover:bg-slate-50
                "
            >

                Cancelar

            </button>

        </div>

    </form>

    {{-- ============================================================
        MEDIA MANAGER
    ============================================================= --}}

    {{--  <div wire:key="user-media-manager-{{ $userId }}">
        <livewire:inv.user.media-manager
            :user-id="$userId"
        />
    </div>  --}}

    {{--  user-media-component.blade.php  --}}
</div>
