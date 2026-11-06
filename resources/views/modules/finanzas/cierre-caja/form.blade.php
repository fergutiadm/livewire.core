{{-- resources/views/modules/finanzas/cierre-caja/form.blade.php --}}
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

        {{-- USAR_FILTROS --}}
        {{-- <div>
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
        </div> --}}

        <div>
    
            {{-- CAJA --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Caja
                </label>

                <select
                    wire:model="cajaId"
                    wire:change="cajaChanged"
                    x-on:change="$dispatch('loading-start', { message: 'Cargando cierres de caja...' })"
                    class="w-full rounded-xl border-slate-300"
                >
                    <option value="">Seleccione una caja</option>

                    @foreach($cajas as $caja)
                        <option value="{{ $caja->id }}">
                            {{ $caja->nombre }}
                        </option>
                    @endforeach
                </select>

                @error('cajaId')
                    <p class="text-xs text-red-500 mt-1">
                        {{ $message }}
                    </p>
                @enderror
            </div>

        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            
            {{-- FECHA --}}
            <div>
                <x-input-flatpickr-range
                    wire:key="fecha-{{ $cierreCajaId ?? 'new' }}"
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
        
        <div class="border border-slate-100 p-3 bg-slate-50 rounded-lg">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                {{-- MONEDA --}}
                <div>

                    <label class="block text-sm font-medium text-slate-700 mb-1" data>
                        Moneda
                    </label>

                    <select
                    wire:model.live="monedaId"
                    class="form-select-responsive"
                    >
                        <option value="">Seleccione</option>

                        @foreach($monedas as $moneda)
                            <option value="{{ $moneda['id'] }}">
                                {{ $moneda['codigo'] }}
                                @if(!$moneda['es_principal'])
                                    - {{ $moneda['tasa_cambio'] }}
                                @endif
                            </option>   
                        @endforeach
                    </select>
                </div>
                
                {{-- ACCION-AGREGAR --}}
                <div>

                    <label class="block text-sm font-medium text-slate-700 mb-1" data>
                        &nbsp;
                    </label>

                    <button
                        type="button"
                        wire:click="$dispatch('cierre-caja-moneda-guardar')"
                        x-on:click="$dispatch('loading-start', {
                            message: 'Guardando moneda...'
                        })"
                        class="btn btn-primary"
                    >
                        {{ $monedaEditandoId ? 'Actualizar' : 'Agregar' }}
                    </button>

                    @if ($monedaEditandoId)
                        <button
                            type="button"
                            wire:click="cancelarEdicionMoneda"
                            class="btn btn-secondary"
                        >
                            Cancelar
                        </button>
                    @endif
                </div>

            </div>

            <div 
                class="grid grid-cols-1 md:grid-cols-2 gap-4 py-2"
                wire:key="cierre-caja-moneda-form-{{ $formCierreMonedaVersion }}"
            >

                {{-- MONTO ESPERADO --}}
                <div>

                    <label class="block text-sm font-medium text-slate-700 mb-1" data>
                        Monto esperado
                    </label>

                    <x-input-clearable
                        type="text"
                        wire:model="monto_esperado_edit"
                        class="w-full rounded-xl border-slate-300"
                        placeholder="Monto esperado"
                    />
                    @error('monto_esperado_edit')<p class="panel-form-error">{{ $message }}</p>@enderror
                </div>

                {{-- MONTO CONTADO --}}
                <div>

                    <label class="block text-sm font-medium text-slate-700 mb-1" data>
                        Monto contado
                    </label>

                    <x-input-clearable
                        type="text"
                        wire:model="monto_contado_edit"
                        class="w-full rounded-xl border-slate-300"
                        placeholder="Monto contado"
                    />
                    @error('monto_contado_edit')<p class="panel-form-error">{{ $message }}</p>@enderror
                </div>

            </div>

            @if(count($cierreMonedas) > 0)
            <div class="grid grid-cols-1 md:grid-cols-4 gap-2 py-2">
                <div>Moneda</div>
                <div>Esperado</div>
                <div>Contado</div>
                <div>Acciones</div>
            </div>
            @foreach($cierreMonedas as $index => $item)
            <div class="grid grid-cols-1 md:grid-cols-4 gap-2 py-1">
                <div>{{ $item['moneda'] }}</div>
                <div>{{ $item['monto_esperado'] }}</div>
                <div>{{ $item['monto_contado'] }}</div>
                <div>
                    <div class="flex items-center justify-center gap-2">
                        <button 
                            type="button"
                            wire:click="$dispatch('cierre-caja-moneda-eliminar', { index: {{ $index}} })" 
                            x-on:click="$dispatch('loading-start', { message: 'Eliminando moneda...' })"
                            class="
                                inline-flex 
                                items-center gap-1 
                                rounded-lg 
                                px-3 
                                py-1.5 
                                text-xs 
                                font-medium 
                                text-white 
                                transition 
                                bg-red-500 
                                hover:bg-red-600
                            "
                        >
                            <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
                            Eliminar
                        </button>

                        <button
                            type="button"
                            wire:click="$dispatch(
                                'cierre-caja-moneda-cargar-edicion',
                                { id: {{ (int) $item['moneda_id'] }} }
                            )"
                        >
                            Editar
                        </button>
                        
                    </div>
                </div>
            </div>
            @endforeach
            @endif
        </div>

        {{-- ============================================================
        PIE DEL FORMULARIO
        ============================================================ --}}
        <div class="flex flex-col gap-4 border-t border-slate-200 pt-5 sm:flex-row justify-end">

            {{-- <div class="text-xs text-slate-500">
                Los datos se guardarán al confirmar.
            </div> --}}

            <div class="flex flex-wrap justify-end items-center gap-3">

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
                        {{ $cierreCajaId ? 'Guardar cambios' : 'Crear cierre de caja' }}
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

    {{--  <div wire:key="caja-media-manager-{{ $cajaId }}">
        <livewire:finanzas.caja.media-manager
            :caja-id="$cajaId"
        />
    </div>  --}}

    {{-- ============================================================
        DETALLES MANAGER
    ============================================================= --}}

    <div wire:key="cierre-caja-detalles-manager">
        <livewire:finanzas.cierre-caja.detalles-manager
            :cierre-caja-id="$cierreCajaId"
        />
    </div>
</div>
