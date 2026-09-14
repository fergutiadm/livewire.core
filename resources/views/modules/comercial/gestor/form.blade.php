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

    <form
        novalidate
        wire:submit.prevent="save"
        x-on:submit="$dispatch('loading-start', { message: 'Guardando gestor...' })"
        class="space-y-6"
    >

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            {{-- USUARIO --}}
            <div>

                <label
                    class="block text-sm font-medium text-slate-700 mb-1"
                >
                    Usuario
                </label>

                <select
                    wire:model="userId"
                    class="w-full rounded-xl border-slate-300"
                    @disabled($editando)
                >

                    <option value="">
                        Seleccione un usuario
                    </option>

                    @foreach($usuarios as $usuario)

                        <option value="{{ $usuario->id }}">
                            {{ $usuario->name }} — {{ $usuario->email }}
                        </option>

                    @endforeach

                </select>

                @error('userId')
                    <p class="panel-form-error">
                        {{ $message }}
                    </p>
                @enderror

            </div>

            {{-- NOMBRE COMERCIAL --}}
            <div>

                <label
                    class="block text-sm font-medium text-slate-700 mb-1"
                >
                    Nombre comercial
                </label>

                <input
                    type="text"
                    wire:model="nombreComercial"
                    class="w-full rounded-xl border-slate-300"
                    placeholder="Nombre comercial del gestor"
                >

                @error('nombreComercial')
                    <p class="panel-form-error">
                        {{ $message }}
                    </p>
                @enderror

            </div>

        </div>


        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- DESCRIPCIÓN --}}
            <div>

                <label
                    class="block text-sm font-medium text-slate-700 mb-1"
                >
                    Descripción
                </label>

                <textarea
                    wire:model="descripcion"
                    rows="3"
                    class="w-full rounded-xl border-slate-300"
                    placeholder="Descripción del gestor"
                ></textarea>

                @error('descripcion')
                    <p class="panel-form-error">
                        {{ $message }}
                    </p>
                @enderror

            </div>


            {{-- ACTIVO --}}
            <div class="flex flex-col items-center">

                <label
                    class="block text-sm font-medium text-slate-700 mb-1"
                >
                    Activo
                </label>

                <input
                    type="checkbox"
                    wire:model="activo"
                    class="w-10 h-10 rounded-md border-slate-300"
                >

                @error('activo')
                    <p class="panel-form-error">
                        {{ $message }}
                    </p>
                @enderror

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

</div>