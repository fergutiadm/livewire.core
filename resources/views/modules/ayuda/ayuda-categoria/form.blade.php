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
        x-on:submit="$dispatch('loading-start', { message: 'Guardando categoría...' })"
        class="space-y-6"
    >


    {{-- ============================================================
        DATOS BÁSICOS
    ============================================================= --}}

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

        {{-- NOMBRE --}}
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">
                Nombre
            </label>

            <input
                type="text"
                wire:model="nombre"
                class="w-full rounded-xl border-slate-300"
                placeholder="Nombre de la categoría"
            >

            @error('nombre')
                <p class="panel-form-error">{{ $message }}</p>
            @enderror
        </div>

        {{-- DESCRIPCIÓN --}}
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">
                Descripción
            </label>

            <textarea
                wire:model="descripcion"
                class="w-full rounded-xl border-slate-300"
                placeholder="Descripción de la categoría"
                rows="3"
            ></textarea>

            @error('descripcion')
                <p class="panel-form-error">{{ $message }}</p>
            @enderror
        </div>

        {{-- ACTIVO --}}
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

    {{-- ============================================================
        ACTIONS
    ============================================================= --}}

    <div class="flex items-center gap-3 pt-2">

        {{-- GUARDAR --}}
        <button
            type="submit"
            wire:loading.attr="disabled"
            wire:target="save"
            class="
                inline-flex
                items-center
                gap-2
                rounded-xl
                bg-indigo-600
                px-5
                py-2
                text-sm
                font-medium
                text-white
                hover:bg-indigo-700
                disabled:opacity-50
                disabled:cursor-not-allowed
            "
        >

            <span
                wire:loading
                wire:target="save"
                class="
                    inline-block
                    w-4
                    h-4
                    border-2
                    border-white
                    border-t-transparent
                    rounded-full
                    animate-spin
                "
            ></span>

            <span>
                Guardar
            </span>

        </button>


        {{-- CANCELAR --}}
        <button
            type="button"
            wire:click="cancel"
            wire:loading.attr="disabled"
            wire:target="cancel"
            class="
                inline-flex
                items-center
                gap-2
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
                disabled:opacity-50
                disabled:cursor-not-allowed
            "
        >
            Cancelar
        </button>

    </div>

</form>

</div>
