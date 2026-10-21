{{-- resources/views/modules/gastos/gasto/form.blade.php --}}
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
        x-on:submit="$dispatch('loading-start', { message: 'Guardando gasto...' })"
        class="space-y-6"
    >

        {{-- USAR_FILTROS --}}
        <div>
            <div class="flex flex-row items-center">

                <label class="block text-sm font-medium text-slate-700 my-1 px-2">
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

        {{-- FLEXBOX FLUIDO PURO Y CENTRADO --}}
        <div class="border border-slate-200 rounded-2xl p-1 bg-white shadow-sm">
            <div class="p-2">
                <label class="block text-sm font-medium text-slate-700 mb-2">Tipo de Gasto</label>
                <div class="w-full">
                    <select wire:model.live="tipoGastoId" class="form-select-responsive">
                        <option value="">Seleccione</option>
                        @foreach($tiposGastos as $tipoGasto)
                            <option value="{{ $tipoGasto->id }}">{{ $tipoGasto->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                @if($tipoGastoId)
                    <div class="mt-4 flex justify-center w-full">
                        <div class="flex flex-wrap items-center justify-center gap-1.5 bg-slate-50 p-1.5 rounded-xl border border-slate-200 shadow-sm w-full max-w-full">
                            <button type="button" wire:click="$set('cantidad', 1)" class="px-1.5 py-1 text-[10px] font-bold text-slate-700 bg-white hover:bg-slate-100 border border-slate-300 rounded-lg shadow-sm transition flex items-center gap-0.5">
                                <svg class="w-3 h-3 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                <span>Cant: 1</span>
                            </button>
                            <button type="button" wire:click="setAtajoGasto(1, 1000, 1)" class="px-1.5 py-1 text-[10px] font-bold text-emerald-700 bg-white hover:bg-emerald-50 border border-emerald-200 rounded-lg shadow-sm transition flex items-center gap-0.5">1k CUP</button>
                            <button type="button" wire:click="setAtajoGasto(1, 10, 2)" class="px-1.5 py-1 text-[10px] font-bold text-blue-700 bg-white hover:bg-blue-50 border border-blue-200 rounded-lg shadow-sm transition flex items-center gap-0.5">$10 USD</button>
                            <button type="button" wire:click="$set('monedaId', 1)" class="px-1.5 py-1 text-[10px] font-semibold text-slate-600 bg-slate-200 hover:bg-slate-300 rounded-lg transition">CUP</button>
                            <button type="button" wire:click="$set('monedaId', 2)" class="px-1.5 py-1 text-[10px] font-semibold text-slate-600 bg-slate-200 hover:bg-slate-300 rounded-lg transition">USD</button>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            
            {{-- ESPECIALISTA --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Especialista
                </label>

                <select
                    wire:model.live="especialistaId"
                    class="form-select-responsive"
                >
                    <option value="">Seleccione</option>

                    @foreach($usuarios as $especialista)
                        <option value="{{ $especialista->id }}">
                            {{ $especialista->name }}
                        </option>
                    @endforeach
                </select>

                @error('especialistaId')
                    <p class="text-xs text-red-500 mt-1">
                        {{ $message }}
                    </p>
                @enderror
            </div>
            
            {{-- RESPONSABLE --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Responsable
                </label>

                <select
                    wire:model.live="responsableId"
                    class="form-select-responsive"
                >
                    <option value="">Seleccione</option>

                    @foreach($usuarios as $responsable)
                        <option value="{{ $responsable->id }}">
                            {{ $responsable->name }}
                        </option>
                    @endforeach
                </select>

                @error('responsableId')
                    <p class="text-xs text-red-500 mt-1">
                        {{ $message }}
                    </p>
                @enderror
            </div>
            
            {{-- CANTIDAD --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Cantidad
                </label>
                <x-input-clearable
                    type="text"
                    wire:model="cantidad"
                    class="form-select-responsive"
                    placeholder="Cantidad"
                />

                @error('cantidad')
                    <p class="text-xs text-red-500 mt-1">
                        {{ $message }}
                    </p>
                @enderror
            </div>
            
            {{-- VALOR --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Valor
                </label>
                <x-input-clearable
                    type="text"
                    wire:model="valor"
                    class="form-select-responsive"
                    placeholder="Valor"
                />

                @error('valor')
                    <p class="text-xs text-red-500 mt-1">
                        {{ $message }}
                    </p>
                @enderror
            </div>
            
            {{-- MONEDA --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Moneda
                </label>

                <select
                    wire:model.live="monedaId"
                    class="form-select-responsive"
                >
                    <option value="">Seleccione</option>

                    @foreach($monedas as $moneda)
                        <option value="{{ $moneda->id }}">
                            {{ $moneda->codigo }}
                            @if(!$moneda->es_principal)
                                - {{ $moneda->tasa_cambio }}
                            @endif
                        </option>   
                    @endforeach
                </select>

                @error('monedaId')
                    <p class="text-xs text-red-500 mt-1">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- FECHA --}}
            <div>
                <x-input-flatpickr-range
                    wire:key="fecha-{{ $gastoId ?? 'new' }}"
                    label="Fecha"
                    wire:model.defer="fecha"
                    :min-date="$fechaMinima"
                    :max-date="$fechaMaxima"
                    placeholder="dd-mm-aaaa"
                />

                @error('fecha')
                    <p class="text-xs text-red-500 mt-1">
                        {{ $message }}
                    </p>
                @enderror
            </div>
        </div>

        <div>

            {{-- DESCRIPCIÓN --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Descripción
                </label>

                <textarea
                    wire:model="descripcion"
                    class="w-full rounded-xl border-slate-300"
                    placeholder="Descripción del gasto"
                    rows="3"
                ></textarea>

                @error('descripcion')
                    <p class="panel-form-error">{{ $message }}</p>
                @enderror
            </div>

        </div>

        {{-- RESUMEN ECONOMICO --}}
        @if($gastoId)
            @php
                $gastoActual = \App\Models\Gasto::find($gastoId);
                $resumenEconomico = $gastoActual?->resumenEconomico();
            @endphp

            <div class="mt-4">
                <span class="mt-4 text-xs font-bold text-slate-400">
                    RESUMEN ECONOMICO
                </span>
            </div>
            @if($resumenEconomico)
                <div class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-3">
                    <div class="rounded-lg border bg-white p-3">
                        <div class="text-xs text-gray-500">
                            Importe CUP
                        </div>

                        <div class="text-lg font-semibold">
                            {{ $resumenEconomico['importe_cup'] }}
                        </div>
                    </div>

                    <div class="rounded-lg border bg-white p-3">
                        <div class="text-xs text-gray-500">
                            Pagado CUP
                        </div>

                        <div class="text-lg font-semibold">
                            {{ $resumenEconomico['pagado_cup'] }}
                        </div>
                    </div>

                    <div class="rounded-lg border bg-white p-3">
                        <div class="text-xs text-gray-500">
                            Pendiente CUP
                        </div>

                        <div class="text-lg font-semibold">
                            {{ $resumenEconomico['saldo_cup'] }}
                        </div>
                    </div>

                </div>
            @endif
        @endif

        {{-- ACTIONS --}}
        <div class="flex items-center justify-center gap-3">

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
