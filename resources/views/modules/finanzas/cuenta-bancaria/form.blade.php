{{-- resources/views/modules/finanzas/cuentas-bancarias/form.blade.php --}}
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
        x-on:submit="$dispatch('loading-start', { message: 'Guardando cuenta...' })"
        class="space-y-6"
    >

        {{-- <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4"> --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

            {{-- PROVINCIA --}}
            <div class="md:col-span-2 xl:col-span-1">
                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Provincia
                </label>

                <select
                    wire:model.live="provinciaId"
                    wire:loading.attr="disabled"
                    wire:target="provinciaId"
                    class="form-select-responsive"
                >
                    <option value="">Seleccione</option>

                    @foreach($provincias as $provincia)
                        <option value="{{ $provincia->id }}">
                            {{ $provincia->nombre }}
                        </option>
                    @endforeach
                </select>

                @error('provinciaId')
                    <p class="text-xs text-red-500 mt-1">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- MUNICIPIO --}}
            <div class="md:col-span-2 xl:col-span-1">
                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Municipio
                </label>

                <select
                    wire:key="cuenta-bancaria-municipio-{{ $provinciaId }}"
                    wire:model.change="municipioId"
                    wire:loading.attr="disabled"
                    wire:target="provinciaId"
                    class="form-select-responsive"
                >
                    <option value="">
                        {{ $provinciaId ? 'Seleccione...' : 'Seleccione primero la provincia...' }}
                    </option>

                    @foreach($municipios as $municipio)
                        <option value="{{ $municipio['id'] }}">
                            {{ $municipio['nombre'] }}
                        </option>
                    @endforeach
                </select>

                @error('municipioId')
                    <p class="text-xs text-red-500 mt-1">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- BANCO --}}
            <div class="md:col-span-2 xl:col-span-1">
                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Banco
                </label>

                <select
                    wire:model.change="bancoId"
                    wire:loading.attr="disabled"
                    wire:target="municipioId"
                    class="form-select-responsive"
                >
                    <option value="">
                        {{ $municipioId ? 'Seleccione...' : 'Seleccione primero el municipio...' }}
                    </option>

                    @foreach($bancos as $banco)
                        <option value="{{ $banco['id'] }}">
                            {{ $banco['nombre'] }}
                        </option>
                    @endforeach
                </select>

                @error('bancoId')
                    <p class="text-xs text-red-500 mt-1">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- SUCURSAL --}}
            <div class="md:col-span-2 xl:col-span-1">
                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Sucursal
                </label>

                <select
                    wire:model.change="sucursal_id"
                    wire:loading.attr="disabled"
                    wire:target="bancoId"
                    class="form-select-responsive"
                >
                    <option value="">
                        {{ $bancoId ? 'Seleccione...' : 'Seleccione primero el banco...' }}
                    </option>

                    @foreach($sucursales as $sucursal)
                        <option value="{{ $sucursal['id'] }}">
                            {{ $sucursal['nombre'] }}
                        </option>
                    @endforeach
                </select>
                @error('sucursal_id')<p class="panel-form-error">{{ $message }}</p>@enderror
            </div>

            {{-- TITULAR --}}
            <div class="md:col-span-2 xl:col-span-1">
                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Titular
                </label>

                <input
                    type="text"
                    wire:model="titular"
                    maxlength="255"
                    class="w-full rounded-xl border-slate-300"
                    placeholder="Titular de la cuenta"
                >

                @error('titular')
                    <p class="panel-form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- NUMERO_CUENTA --}}
            <div class="md:col-span-2 xl:col-span-1">

                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Número de la cuenta
                </label>

                <input
                    type="text"
                    wire:model="numero_cuenta"
                    maxlength="150"
                    class="w-full rounded-xl border-slate-300"
                    placeholder="Número de la cuenta"
                >
                @error('numero_cuenta')<p class="panel-form-error">{{ $message }}</p>@enderror
            </div>

            {{-- MONEDA --}}
            <div class="md:col-span-2 xl:col-span-1">
                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Moneda
                </label>

                <select
                    wire:model.live="moneda_id"
                    wire:loading.attr="disabled"
                    wire:target="moneda_id"
                    class="form-select-responsive"
                >
                    <option value="">Seleccione</option>

                    @foreach($monedas as $moneda)
                        <option value="{{ $moneda['id'] }}">
                            {{ $moneda['nombre'] }}
                        </option>
                    @endforeach
                </select>
                @error('moneda_id')<p class="panel-form-error">{{ $message }}</p>@enderror
            </div>

            {{-- ACTIVA --}}
            <div class="md:col-span-2 xl:col-span-1 flex flex-col items-center">

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

            {{-- DESCRIPCION --}}
            <div class="sm:col-span-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Descripción
                </label>

                <input
                    type="text"
                    wire:model="descripcion"
                    maxlength="255"
                    class="w-full rounded-xl border-slate-300"
                    placeholder="Descripción del cuenta"
                >

                @error('descripcion')
                    <p class="panel-form-error">{{ $message }}</p>
                @enderror
            </div>

        </div>

        {{-- ============================================================
        PIE DEL FORMULARIO
        ============================================================ --}}
        <div class="flex flex-col gap-4 border-t border-slate-200 pt-5 sm:flex-row justify-end">

            {{-- <div class="text-xs text-slate-500">
                Los datos se guardarán al confirmar.
            </div> --}}

            <div class="flex flex-wrap justify-end items-center gap-3">

                {{-- USAR_FILTROS --}}
                <div>
                    <div class="flex flex-row items-center">

                        <label class="block text-sm font-medium text-slate-700 my-1 px-2 text-nowrap">
                            Filtrar Resultado
                        </label>

                        <input
                            type="checkbox"
                            wire:model="usar_filtros"
                            wire:change="aplicarFiltrosResultado()"
                            class="w-5 h-5 rounded-md border-slate-300"
                        >
                    </div>
                </div>

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
                        {{ $cuentaBancariaId ? 'Guardar cambios' : 'Crear Cuenta' }}
                    </span>

                    <span wire:loading wire:target="save">
                        <i class="bi bi-arrow-repeat"></i>
                        Guardando...
                    </span>

                </button>

            </div>

        </div>

    </form>
</div>
