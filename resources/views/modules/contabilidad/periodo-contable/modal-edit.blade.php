
{{-- resources/views/modules/contabilidad/periodo-contable/modal-edit.blade.php --}}

<div
    class="fixed inset-0 z-[9999] flex items-center justify-center
           overflow-y-auto bg-slate-950/60 p-2 backdrop-blur-sm sm:p-5"
    wire:click.self="cancel"
    @keydown.escape.window="$wire.cancel()"
>
    {{-- CONTENEDOR PRINCIPAL --}}
    <section
        role="dialog"
        aria-modal="true"
        aria-labelledby="periodo-edit-modal-title"
        class="flex max-h-[calc(100dvh-1rem)] w-full max-w-lg
               flex-col overflow-hidden rounded-2xl
               border border-white/70 bg-white shadow-2xl
               shadow-slate-950/20 sm:max-h-[90vh] sm:rounded-3xl"
    >

        {{-- =====================================================
            ENCABEZADO CON DEGRADADO
        ====================================================== --}}
        <header
            class="relative shrink-0 overflow-hidden border-b
                   border-slate-200 bg-gradient-to-br
                   from-emerald-50 via-white to-indigo-50
                   px-4 py-4 sm:px-6 sm:py-5"
        >
            {{-- Efecto decorativo --}}
            <div
                class="pointer-events-none absolute -right-8 -top-12
                       h-36 w-36 rounded-full bg-emerald-100/60 blur-2xl"
            ></div>

            <div class="relative flex items-start gap-3">

                {{-- Icono --}}
                <div
                    class="flex h-11 w-11 shrink-0 items-center
                           justify-center rounded-xl bg-emerald-100
                           text-emerald-700 ring-1 ring-emerald-200/70"
                >
                    <i class="bi bi-calendar-range text-xl"></i>
                </div>

                {{-- Título --}}
                <div class="min-w-0 flex-1">

                    <h2
                        id="periodo-edit-modal-title"
                        class="text-lg font-bold text-slate-900"
                    >
                        @if($periodoContableId)
                            Editar período contable
                        @else
                            Nuevo período contable
                        @endif
                    </h2>

                    <p class="mt-1 text-xs leading-5 text-slate-600">
                        @if($periodoContableId)
                            Modifica la información del período seleccionado.
                        @else
                            Define las fechas y duración del nuevo período.
                        @endif
                    </p>

                </div>

                {{-- Cerrar --}}
                <button
                    type="button"
                    wire:click="cancel"
                    aria-label="Cerrar"
                    class="flex h-10 w-10 shrink-0 items-center
                           justify-center rounded-xl border
                           border-slate-200 bg-white/90 text-slate-500
                           transition hover:bg-white hover:text-slate-900"
                >
                    <i class="bi bi-x-lg"></i>
                </button>

            </div>
        </header>

        {{-- =====================================================
            FORMULARIO
        ====================================================== --}}
        <form
            novalidate
            wire:submit.prevent="save"
            x-on:submit="$dispatch('loading-start', { message: 'Guardando período contable...' })"
            class="flex min-h-0 flex-1 flex-col"
        >

            {{-- CONTENIDO DESPLAZABLE --}}
            <div
                class="min-h-0 flex-1 space-y-5 overflow-y-auto
                       overscroll-contain px-4 py-5 sm:px-6"
            >

                {{-- NOMBRE --}}
                <div>
                    <label
                        for="periodo-nombre"
                        class="mb-1.5 block text-sm font-semibold text-slate-700"
                    >
                        Nombre del período
                    </label>

                    <input
                        id="periodo-nombre"
                        type="text"
                        wire:model="nombre"
                        placeholder="Ej. Período 2026-W44"
                        @class([
                            'block w-full rounded-xl border bg-white px-3 py-2.5 text-sm shadow-sm',
                            'border-red-400 focus:border-red-500 focus:ring-red-500' => $errors->has('nombre'),
                            'border-slate-300 focus:border-emerald-500 focus:ring-emerald-500' => !$errors->has('nombre'),
                        ])
                    />

                    @error('nombre')
                        <p class="mt-1 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- DURACIÓN --}}
                <div
                    class="rounded-xl border border-slate-200
                           bg-slate-50/70 p-4"
                >
                    <div class="flex items-center justify-between gap-4">

                        <div class="min-w-0">
                            <label
                                for="periodo-duracion"
                                class="block text-sm font-semibold text-slate-700"
                            >
                                Duración del período
                            </label>

                            <p class="mt-1 text-xs text-slate-500">
                                Cantidad de días, entre 1 y 30.
                            </p>
                        </div>

                        <div class="w-24 shrink-0">
                            <input
                                id="periodo-duracion"
                                type="number"
                                min="1"
                                max="30"
                                step="1"
                                wire:model="duracion_periodo"
                                wire:change="duracionChanged"
                                class="block w-full rounded-xl
                                       border-slate-300 bg-white
                                       px-3 py-2.5 text-center text-sm
                                       font-semibold text-slate-800
                                       focus:border-emerald-500
                                       focus:ring-emerald-500"
                            />
                        </div>
                    </div>

                    @error('duracion_periodo')
                        <p class="mt-2 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- FECHAS --}}
                <div>
                    <h3 class="mb-3 flex items-center gap-2 text-sm font-semibold text-slate-700">
                        <i class="bi bi-calendar3 text-emerald-600"></i>
                        Rango de fechas
                    </h3>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

                        <div class="min-w-0">
                            <x-input-flatpickr
                                wire:key="fecha-inicio-{{ $periodoContableId ?? 'new' }}"
                                wire:change="fechaInicioCambiada"
                                label="Fecha Inicio"
                                wire:model.defer="fecha_inicio"
                                placeholder="dd-mm-aaaa"
                            />

                            @error('fecha_inicio')
                                <p class="mt-1 text-xs text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="min-w-0">
                            <x-input-flatpickr
                                wire:key="fecha-fin-{{ $periodoContableId ?? 'new' }}"
                                wire:change="fechaFinCambiada"
                                label="Fecha Fin"
                                wire:model.defer="fecha_fin"
                                placeholder="dd-mm-aaaa"
                            />

                            @error('fecha_fin')
                                <p class="mt-1 text-xs text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                    </div>
                </div>

                {{-- INFORMACIÓN --}}
                <div
                    class="flex items-start gap-2 rounded-xl
                           border border-indigo-100 bg-indigo-50/60
                           px-3 py-3"
                >
                    <i class="bi bi-info-circle mt-0.5 text-indigo-600"></i>

                    <p class="text-xs leading-5 text-indigo-800">
                        La duración permite calcular la fecha de
                        finalización a partir de la fecha de inicio.
                    </p>
                </div>

            </div>

            {{-- =====================================================
                PIE FIJO
            ====================================================== --}}
            <footer
                class="flex shrink-0 flex-col-reverse gap-2
                       border-t border-slate-200 bg-slate-50/70
                       px-4 py-3 sm:flex-row sm:justify-end
                       sm:px-6 sm:py-4"
            >

                <button
                    type="button"
                    wire:click="cancel"
                    class="inline-flex min-h-10 items-center
                           justify-center rounded-xl border
                           border-slate-300 bg-white px-5 py-2
                           text-sm font-semibold text-slate-700
                           transition hover:bg-slate-100"
                >
                    Cancelar
                </button>

                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    wire:target="save"
                    class="inline-flex min-h-10 items-center
                           justify-center gap-2 rounded-xl
                           bg-emerald-600 px-5 py-2
                           text-sm font-semibold text-white
                           shadow-sm transition hover:bg-emerald-700
                           disabled:cursor-wait disabled:opacity-60"
                >
                    <i class="bi bi-check2-circle"></i>

                    <span wire:loading.remove wire:target="save">
                        {{ $periodoContableId ? 'Guardar cambios' : 'Crear período' }}
                    </span>

                    <span wire:loading wire:target="save">
                        Guardando...
                    </span>
                </button>

            </footer>

        </form>
    </section>
</div>
