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
        x-on:submit="$dispatch('loading-start', { message: 'Guardando atributo...' })"
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
                    placeholder="Nombre del atributo"
                >
                @error('nombre')<p class="panel-form-error">{{ $message }}</p>@enderror
            </div>

            {{-- DESCRIPCION --}}
            <div>

                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Descripción
                </label>

                <textarea
                    wire:model="descripcion"
                    class="w-full rounded-xl border-slate-300"
                    placeholder="Descripción del atributo"
                    rows="3"
                ></textarea>
                @error('descripcion')<p class="panel-form-error">{{ $message }}</p>@enderror
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

    {{--  <div wire:key="atributo-media-manager-{{ $atributoId }}">
        <livewire:inv.atributo.media-manager
            :atributo-id="$atributoId"
        />
    </div>  --}}

    {{--  atributo-media-component.blade.php  --}}
</div>
