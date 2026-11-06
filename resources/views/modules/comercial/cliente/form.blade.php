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
        x-on:submit="$dispatch('loading-start', { message: 'Guardando cliente...' })"
        class="space-y-6"
    >

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-2 gap-4">

            {{-- NOMBRE --}}
            <div>

                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Nombre
                </label>

                <input
                    type="text"
                    wire:model="nombre"
                    class="w-full rounded-xl border-slate-300"
                    placeholder="Nombre del cliente"
                >
                @error('nombre')<p class="panel-form-error">{{ $message }}</p>@enderror
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
                        placeholder="Correo del cliente"
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

        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-2 gap-4">

            {{-- MOVIL --}}
            <div>

                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Móvil
                </label>

                <input
                    type="text"
                    wire:model="telefono"
                    class="w-full rounded-xl border-slate-300"
                    placeholder="Móvil del cliente"
                >
                @error('telefono')<p class="panel-form-error">{{ $message }}</p>@enderror
            </div>

            {{-- CI --}}
            <div>

                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Carnet de identidad
                </label>

                <input
                    type="text"
                    wire:model="ci"
                    class="w-full rounded-xl border-slate-300"
                    placeholder="CI del cliente"
                >
                @error('ci')<p class="panel-form-error">{{ $message }}</p>@enderror
            </div>

        </div>

        {{-- ============================================================
        PIE DEL FORMULARIO
        ============================================================ --}}
        <div class="flex flex-col gap-4 border-t border-slate-200 pt-5 sm:flex-row sm:items-center sm:justify-between">

            <div class="text-xs text-slate-500">
                Los datos se guardarán al confirmar.
            </div>

            <div class="flex flex-wrap items-center gap-3">

                {{-- CANCELAR --}}
                <button
                    type="button"
                    wire:click="cancel"
                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-300"
                >
                    <i class="bi bi-x-lg"></i>
                    Cancelar
                </button>

                {{-- GUARDAR --}}
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    wire:target="save"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60"
                >

                    <span wire:loading.remove wire:target="save">
                        <i class="bi bi-floppy"></i>
                        {{ $clienteId ? 'Guardar cambios' : 'Crear cliente' }}
                    </span>

                    <span wire:loading wire:target="save">
                        <i class="bi bi-arrow-repeat"></i>
                        Guardando...
                    </span>

                </button>

            </div>

        </div>

    </form>

    {{-- ============================================================
        MEDIA MANAGER
    ============================================================= --}}

    {{--  <div wire:key="cliente-media-manager-{{ $clienteId }}">
        <livewire:inv.cliente.media-manager
            :cliente-id="$clienteId"
        />
    </div>  --}}

    {{--  cliente-media-component.blade.php  --}}
</div>
