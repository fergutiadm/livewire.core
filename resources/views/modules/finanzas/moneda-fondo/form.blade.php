{{-- resources/views/modules/finanzas/moneda-fondo/form.blade.php --}}
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
        x-on:submit="$dispatch('loading-start', { message: 'Registrando movimiento...' })"
        class="space-y-6"
    >

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        {{-- <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4"> --}}

            {{-- LOCAL --}}
            {{-- <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Local
                </label>

                <select
                    wire:model.live="localId"
                    wire:loading.attr="disabled"
                    wire:target="localId"
                    class="w-full rounded-xl border-slate-300"
                >
                    <option value="">Seleccione...</option>

                    @foreach($locales as $local)
                        <option value="{{ $local['id'] }}">
                            {{ $local['nombre'] }}
                        </option>
                    @endforeach
                </select>

                @error('localId')
                    <p class="panel-form-error">{{ $message }}</p>
                @enderror
            </div> --}}

            {{-- PERIODO --}}
            {{-- <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Periodo contable
                </label>

                <select
                    wire:model.change="periodoId"
                    wire:loading.attr="disabled"
                    wire:target="localId"
                    class="w-full rounded-xl border-slate-300"
                    @disabled(!$localId)
                >
                    <option value="">
                        {{ $localId ? 'Seleccione...' : 'Seleccione primero el local...' }}
                    </option>

                    @foreach($periodos as $periodo)
                        <option value="{{ $periodo['id'] }}">
                            {{ $periodo['nombre'] }}
                            ({{ format_fecha($periodo['fecha_inicio']) }} → {{ format_fecha($periodo['fecha_fin']) }})
                        </option>
                    @endforeach
                </select>

                @error('periodoId')
                    <p class="panel-form-error">{{ $message }}</p>
                @enderror
            </div> --}}

            {{-- USAR_FILTROS --}}
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

            {{-- CONCEPTO --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Concepto
                </label>

                <select
                    wire:model="conceptoId"
                    class="w-full rounded-xl border-slate-300"
                >
                    <option value="">Seleccione...</option>

                    @foreach($conceptos as $concepto)
                        <option value="{{ $concepto['id'] }}">
                            {{ $concepto['nombre'] }}
                            — {{ $concepto['entrada_salida'] ? 'Entrada' : 'Salida' }}
                        </option>
                    @endforeach
                </select>

                @error('conceptoId')
                    <p class="panel-form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- FECHA --}}
            <div wire:key="fecha-{{ $periodoId ?? 'new' }}">
                {{-- <label class="block text-sm font-medium text-slate-700 mb-1">
                    Fecha
                </label>

                <input
                    type="date"
                    wire:model="fecha"
                    class="w-full rounded-xl border-slate-300"
                > --}}
                <x-input-flatpickr-range
                    label="Fecha"
                    wire:model.defer.change="fecha"
                    :min-date="$fechaMinima"
                    :max-date="$fechaMaxima"
                    placeholder="dd-mm-aaaa"
                />

                @error('fecha')
                    <p class="panel-form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- MONEDA --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Moneda
                </label>

                <select
                    wire:model.change="monedaId"
                    class="w-full rounded-xl border-slate-300"
                >
                    <option value="">Seleccione...</option>

                    @foreach($monedas as $moneda)
                        <option value="{{ $moneda['id'] }}">
                            {{ $moneda['codigo'] }} — {{ $moneda['nombre'] }}
                        </option>
                    @endforeach
                </select>

                @error('monedaId')
                    <p class="panel-form-error">{{ $message }}</p>
                @enderror
            </div>


            {{-- MONTO --}}
            <div class="col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Monto
                </label>

                <div class="flex items-center gap-2">

                    <input
                        type="number"
                        step="10"
                        min="10"
                        wire:model="monto"
                        class="w-full rounded-xl border-slate-300"
                        placeholder="0.00"
                    >

                    <button
                        type="button"
                        wire:click="abrirCalculadora"
                        wire:loading.attr="disabled"
                        wire:target="abrirCalculadora"
                        class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl border border-indigo-200 bg-indigo-50 px-3 py-2.5 text-xs font-medium text-indigo-700 transition hover:bg-indigo-100 disabled:cursor-not-allowed disabled:opacity-60"
                        title="Calcular monto por denominaciones"
                    >
                        <i class="bi bi-calculator"></i>
                        <span class="hidden sm:inline">
                            Calcular
                        </span>
                    </button>

                </div>

                @error('monto')
                    <p class="panel-form-error">{{ $message }}</p>
                @enderror
            </div>


            {{-- TASA --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Tasa de cambio
                </label>

                <input
                    type="number"
                    step="5"
                    min="5"
                    wire:model="tasaCambio"
                    class="w-full rounded-xl border-slate-300"
                    placeholder="1"
                >

                @error('tasaCambio')
                    <p class="panel-form-error">{{ $message }}</p>
                @enderror
                </div>

                {{-- DESCRIPCION --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1">
                        Descripción
                    </label>

                    <input
                        type="text"
                        wire:model="descripcion"
                        maxlength="255"
                        class="w-full rounded-xl border-slate-300"
                        placeholder="Descripción del movimiento"
                    >

                    @error('descripcion')
                        <p class="panel-form-error">{{ $message }}</p>
                    @enderror
                </div>

        </div>

        @error('movimiento')
            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3">
                <p class="text-sm text-red-700">
                    {{ $message }}
                </p>
            </div>
        @enderror

        {{-- PIE --}}
        <div class="flex flex-col gap-3 border-t border-slate-200 pt-5 sm:flex-row justify-end">

            <div class="flex flex-wrap justify-end items-center gap-3">

                <button
                    type="button"
                    wire:click="cancel"
                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-2 py-2.5 text-xs font-medium text-slate-700 transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-300"
                >
                    <i class="bi bi-x-lg"></i>
                    Cancelar
                </button>

                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    wire:target="save"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-2 py-2.5 text-xs font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60"
                >

                    <span wire:loading.remove wire:target="save">
                        <i class="bi bi-floppy"></i>
                        Registrar movimiento
                    </span>

                    <span wire:loading wire:target="save">
                        <i class="bi bi-arrow-repeat"></i>
                        Registrando...
                    </span>

                </button>

            </div>

        </div>

    </form>

    {{-- CALCULADORA POR DENOMINACIONES --}}
    <livewire:finanzas.denominacion.calculadora
        :monedaFija="true"/>
</div>