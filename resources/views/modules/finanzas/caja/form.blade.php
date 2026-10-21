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
        x-on:submit="$dispatch('loading-start', { message: 'Guardando caja...' })"
        class="space-y-6"
    >

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    
            {{-- LOCAL --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Local
                </label>

                <select
                    wire:model="localId"
                    wire:change="localChanged"
                    class="w-full rounded-xl border-slate-300"
                >
                    <option value="">Seleccione un local</option>

                    @foreach($locales as $local)
                        <option value="{{ $local->id }}">
                            {{ $local->nombre }}
                        </option>
                    @endforeach
                </select>

                @error('localId')
                    <p class="text-xs text-red-500 mt-1">
                        {{ $message }}
                    </p>
                @enderror
            </div>
            

            {{-- ACTIVA --}}
            <div class="flex flex-col items-center">

                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Activa
                </label>

                <input
                    type="checkbox"
                    wire:model="activa"
                    class="w-10 h-10 rounded-md border-slate-300"
                >
                @error('activa')<p class="panel-form-error">{{ $message }}</p>@enderror
            </div>

        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            {{-- CODIGO --}}
            <div>

                <label class="block text-sm font-medium text-slate-700 mb-1" data>
                    Código
                </label>

                <input
                    type="text"
                    wire:model="codigo"
                    class="w-full rounded-xl border-slate-300"
                    placeholder="Codigo de la caja"
                >
                @error('codigo')<p class="panel-form-error">{{ $message }}</p>@enderror
            </div>

            {{-- NOMBRE --}}
            <div>

                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Nombre
                </label>

                <input
                    type="text"
                    wire:model="nombre"
                    class="w-full rounded-xl border-slate-300"
                    placeholder="Nombre de la caja"
                >
                @error('nombre')<p class="panel-form-error">{{ $message }}</p>@enderror
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

    {{--  <div wire:key="caja-media-manager-{{ $cajaId }}">
        <livewire:finanzas.caja.media-manager
            :caja-id="$cajaId"
        />
    </div>  --}}

    {{--  caja-media-component.blade.php  --}}
</div>
