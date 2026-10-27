{{-- resources/views/navigation/fecha-operacion.blade.php --}}

@php
    $fi = $fecha_inicio ?? $fecha_inicial ?? null;
    $ff = $fecha_fin ?? $fecha_final ?? null;
    $fo = $fechaOperacion ?? null;

    /*
     * DATOS DE DEMOSTRACIÓN.
     * Sustituir posteriormente por los resúmenes reales.
     */
    $ventasDemo = [12500, 0, 8400, 16300, 5200];
    $gastosDemo = [1800, 950, 0, 2400, 700];
    $gananciasDemo = [10700, -950, 8400, 13900, 4500];

    $fechas = array_values($fechas_periodo ?? []);
    $indiceSeleccionado = 0;

    foreach ($fechas as $indice => $fecha) {
        if (($fecha['en'] ?? null) === $fo) {
            $indiceSeleccionado = $indice;
            break;
        }
    }

    $indiceDemo = $indiceSeleccionado % count($ventasDemo);

    $ventasSeleccionadas = $ventasDemo[$indiceDemo];
    $gastosSeleccionados = $gastosDemo[$indiceDemo];
    $gananciaSeleccionada = $gananciasDemo[$indiceDemo];
@endphp

<div class="space-y-3">

    {{-- Selector de fecha --}}
    <button
        type="button"
        wire:click="openModal"
        aria-label="Seleccionar fecha de operación"
        class="group flex w-full items-center justify-between gap-3
               rounded-xl border border-slate-200 bg-white px-3 py-2.5
               text-left shadow-sm transition
               hover:border-emerald-300 hover:bg-emerald-50/40
               focus:outline-none focus:ring-2 focus:ring-emerald-500/30"
    >
        <span class="flex min-w-0 items-center gap-3">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center
                         rounded-xl bg-emerald-50 text-emerald-700">
                <svg
                    class="h-5 w-5"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    aria-hidden="true"
                >
                    <rect x="3" y="5" width="18" height="16" rx="2"/>
                    <path d="M16 3v4M8 3v4M3 10h18"/>
                </svg>
            </span>

            <span class="min-w-0">
                <span class="block text-xs font-medium text-slate-500">
                    Fecha de operación
                </span>

                <span class="block truncate text-sm font-semibold text-slate-800">
                    {{ $fo ? format_fecha($fo) : 'Definir fecha' }}
                </span>
            </span>
        </span>

        <svg
            class="h-5 w-5 shrink-0 text-slate-400 transition group-hover:text-emerald-600"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.8"
            aria-hidden="true"
        >
            <path d="m7 10 5 5 5-5"/>
        </svg>
    </button>

    {{-- Mostrar/Ocultar resumen --}}
    {{-- Comentado el boton hasta encontrar mejor forma de presentarlo sin que rompa el disenyo --}}
    {{-- <button
        type="button"
        wire:click="$toggle('mostrarResumen')"
        aria-expanded="{{ $mostrarResumen ? 'true' : 'false' }}"
        class="inline-flex items-center gap-2 rounded-lg px-2 py-1.5
            text-xs font-semibold text-indigo-700 transition
            hover:bg-indigo-50"
    >
        @if ($mostrarResumen)
            <span>Ocultar resumen</span>
            <span aria-hidden="true">⌃</span>
        @else
            <span>Ver resumen del día</span>
            <span aria-hidden="true">⌄</span>
        @endif
    </button> --}}

    @if ($mostrarResumen)
        {{-- Resumen visual de la fecha seleccionada --}}
        <section
            class="overflow-hidden rounded-2xl border border-slate-200
                bg-white shadow-sm"
            aria-label="Resumen demostrativo de la fecha seleccionada"
        >
            <div class="flex items-center justify-between gap-3 border-b
                        border-slate-100 px-4 py-3">
                <div>
                    <h3 class="text-sm font-semibold text-slate-800">
                        Resumen del día
                    </h3>

                    <p class="mt-0.5 text-xs text-slate-500">
                        {{ $fo ? format_fecha($fo) : 'Sin fecha seleccionada' }}
                    </p>
                </div>

                <span class="inline-flex items-center rounded-full
                            bg-amber-50 px-2 py-1 text-[10px]
                            font-semibold uppercase tracking-wide text-amber-700">
                    Demo
                </span>
            </div>

            <div class="grid grid-cols-3 divide-x divide-slate-100">
                {{-- Ventas --}}
                <div class="min-w-0 px-2 py-3 sm:px-4">
                    <div class="flex items-center gap-1.5 text-xs text-slate-500">
                        <span class="h-2 w-2 shrink-0 rounded-full bg-emerald-500"></span>
                        Ventas
                    </div>

                    <p class="mt-2 break-words text-sm font-bold tabular-nums
                            text-slate-900 sm:text-lg">
                        {{ number_format($ventasSeleccionadas, 2, ',', '.') }}
                    </p>

                    <p class="mt-0.5 text-[10px] text-slate-400">CUP · Demo</p>
                </div>

                {{-- Gastos --}}
                <div class="min-w-0 px-2 py-3 sm:px-4">
                    <div class="flex items-center gap-1.5 text-xs text-slate-500">
                        <span class="h-2 w-2 shrink-0 rounded-full bg-rose-500"></span>
                        Gastos
                    </div>

                    <p class="mt-2 break-words text-sm font-bold tabular-nums
                            text-slate-900 sm:text-lg">
                        {{ number_format($gastosSeleccionados, 2, ',', '.') }}
                    </p>

                    <p class="mt-0.5 text-[10px] text-slate-400">CUP · Demo</p>
                </div>

                {{-- Ganancia --}}
                <div class="min-w-0 px-2 py-3 sm:px-4">
                    <div class="flex items-center gap-1.5 text-xs text-slate-500">
                        <span class="h-2 w-2 shrink-0 rounded-full bg-indigo-500"></span>
                        Ganancia
                    </div>

                    <p @class([
                        'mt-2 break-words text-sm font-bold tabular-nums sm:text-lg',
                        'text-rose-600' => $gananciaSeleccionada < 0,
                        'text-slate-900' => $gananciaSeleccionada >= 0,
                    ])>
                        {{ number_format($gananciaSeleccionada, 2, ',', '.') }}
                    </p>

                    <p class="mt-0.5 text-[10px] text-slate-400">CUP · Demo</p>
                </div>
            </div>
        </section>
    @endif

    {{-- Modal de selección --}}
    @if ($showModal)
        <div
            class="fixed inset-0 z-[9999] flex items-center justify-center
                   bg-slate-950/50 p-3 backdrop-blur-[2px] sm:p-5"
            wire:click.self="closeModal"
            wire:keydown.escape.window="closeModal"
        >
            <section
                class="flex max-h-[90vh] w-full max-w-xl flex-col
                       overflow-hidden rounded-2xl bg-white shadow-2xl"
                role="dialog"
                aria-modal="true"
                aria-labelledby="fecha-modal-title"
            >
                {{-- Encabezado del modal --}}
                <header class="flex items-center justify-between gap-3
                               border-b border-slate-100 px-5 py-4">
                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center
                                     rounded-xl bg-emerald-50 text-emerald-700">
                            <svg
                                class="h-5 w-5"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                aria-hidden="true"
                            >
                                <rect x="3" y="5" width="18" height="16" rx="2"/>
                                <path d="M16 3v4M8 3v4M3 10h18"/>
                            </svg>
                        </span>

                        <div>
                            <h2
                                id="fecha-modal-title"
                                class="text-base font-semibold text-slate-900"
                            >
                                Seleccionar fecha
                            </h2>

                            <p class="mt-0.5 text-xs text-slate-500">
                                Consulta los indicadores de cada día
                            </p>
                        </div>
                    </div>

                    <button
                        type="button"
                        wire:click="closeModal"
                        aria-label="Cerrar selector de fecha"
                        class="rounded-lg p-2 text-slate-400 transition
                               hover:bg-slate-100 hover:text-slate-700"
                    >
                        <svg
                            class="h-5 w-5"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            aria-hidden="true"
                        >
                            <path d="m18 6-12 12M6 6l12 12"/>
                        </svg>
                    </button>
                </header>

                {{-- Lista de fechas --}}
                <div class="min-h-0 flex-1 space-y-2 overflow-y-auto p-4">
                    @forelse ($fechas as $fecha)
                        @php
                            $indiceDia = $loop->index % count($ventasDemo);

                            $ventasDia = $ventasDemo[$indiceDia];
                            $gastosDia = $gastosDemo[$indiceDia];
                            $gananciaDia = $gananciasDemo[$indiceDia];

                            $esSeleccionada = $fo === ($fecha['en'] ?? null);
                        @endphp

                        <button
                            type="button"
                            wire:key="fecha-operacion-{{ $fecha['en'] }}"
                            wire:click="seleccionarFechaOperacion('{{ $fecha['en'] }}')"
                            @class([
                                'w-full rounded-xl border p-3 text-left transition',
                                'border-emerald-300 bg-emerald-50/70 ring-1 ring-emerald-200' => $esSeleccionada,
                                'border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50' => ! $esSeleccionada,
                            ])
                        >
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex justify-between min-w-0 items-center gap-3">
                                    <span @class([
                                        'flex h-10 w-10 shrink-0 items-center justify-center rounded-xl',
                                        'bg-emerald-100 text-emerald-700' => $esSeleccionada,
                                        'bg-slate-100 text-slate-500' => ! $esSeleccionada,
                                    ])>
                                        <svg
                                            class="h-5 w-5"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                            aria-hidden="true"
                                        >
                                            <rect x="3" y="5" width="18" height="16" rx="2"/>
                                            <path d="M16 3v4M8 3v4M3 10h18"/>
                                        </svg>
                                    </span>

                                    <span class="min-w-0">
                                        <span class="block text-sm font-semibold
                                                     text-slate-800">
                                           {{ $fecha['es'] ?? '—' }}
                                        </span>

                                        <span class="mt-0.5 block text-xs text-slate-500">
                                            {{ $esSeleccionada ? 'Fecha seleccionada' : 'Ver este día' }}
                                        </span>
                                    </span>
                                    <span class="min-w-0 ml-2">
                                        <span class="block text-[10px] font-medium
                                                     text-slate-500">
                                            Ventas
                                        </span>

                                        <span class="mt-0.5 block break-words text-xs font-semibold tabular-nums text-emerald-700 sm:text-sm">
                                            {{ number_format($ventasDia, 0, ',', '.') }}
                                        </span>
                                    </span>
                                    <span class="min-w-0 ml-2">
                                        <span class="block text-[10px] font-medium
                                                     text-slate-500">
                                            Gastos
                                        </span>

                                        <span class="mt-0.5 block break-words text-xs font-semibold tabular-nums text-rose-600 sm:text-sm">
                                            {{ number_format($gastosDia, 0, ',', '.') }}
                                        </span>
                                    </span>
                                    <span class="min-w-0 ml-2">
                                        <span class="block text-[10px] font-medium
                                                     text-slate-500">
                                            Ganancia
                                        </span>

                                        <span
                                            @class([
                                                    'mt-0.5 block break-words text-xs font-semibold tabular-nums sm:text-sm',
                                                    'text-rose-600' => $gananciaDia < 0,
                                                    'text-indigo-700' => $gananciaDia >= 0,
                                                ])>
                                            {{ number_format($gananciaDia, 0, ',', '.') }}
                                        </span>
                                    </span>
                                </div>

                                @if ($esSeleccionada)
                                    <span class="inline-flex shrink-0 items-center gap-1
                                                 rounded-full bg-emerald-100 px-2 py-1
                                                 text-[10px] font-semibold text-emerald-800">
                                        <svg
                                            class="h-3 w-3"
                                            viewBox="0 0 20 20"
                                            fill="currentColor"
                                            aria-hidden="true"
                                        >
                                            <path
                                                fill-rule="evenodd"
                                                d="M16.704 5.29a1 1 0 0 1 .006 1.414l-7.1 7.16a1 1 0 0 1-1.42.003L3.29 8.98a1 1 0 1 1 1.42-1.407l4.188 4.23 6.393-6.507a1 1 0 0 1 1.413-.006Z"
                                                clip-rule="evenodd"
                                            />
                                        </svg>
                                        Actual
                                    </span>
                                @endif
                            </div>

                            {{-- Indicadores demostrativos de cada fecha --}}
                            {{-- <div class="mt-3 grid grid-cols-3 gap-2 border-t
                                        border-slate-200/70 pt-3">
                                <div class="min-w-0">
                                    <p class="text-[10px] font-medium text-slate-500">
                                        Ventas
                                    </p>
                                    <p class="mt-1 break-words text-xs font-semibold
                                              tabular-nums text-emerald-700 sm:text-sm">
                                        {{ number_format($ventasDia, 0, ',', '.') }}
                                    </p>
                                </div>

                                <div class="min-w-0">
                                    <p class="text-[10px] font-medium text-slate-500">
                                        Gastos
                                    </p>
                                    <p class="mt-1 break-words text-xs font-semibold
                                              tabular-nums text-rose-600 sm:text-sm">
                                        {{ number_format($gastosDia, 0, ',', '.') }}
                                    </p>
                                </div>

                                <div class="min-w-0">
                                    <p class="text-[10px] font-medium text-slate-500">
                                        Ganancia
                                    </p>
                                    <p @class([
                                        'mt-1 break-words text-xs font-semibold tabular-nums sm:text-sm',
                                        'text-rose-600' => $gananciaDia < 0,
                                        'text-indigo-700' => $gananciaDia >= 0,
                                    ])>
                                        {{ number_format($gananciaDia, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div> --}}
                        </button>
                    @empty
                        <div class="rounded-xl border border-dashed border-slate-300
                                    px-4 py-10 text-center">
                            <div class="mx-auto flex h-12 w-12 items-center
                                        justify-center rounded-full bg-slate-100
                                        text-slate-400">
                                <svg
                                    class="h-6 w-6"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    aria-hidden="true"
                                >
                                    <rect x="3" y="5" width="18" height="16" rx="2"/>
                                    <path d="M16 3v4M8 3v4M3 10h18"/>
                                </svg>
                            </div>

                            <p class="mt-3 text-sm font-semibold text-slate-700">
                                No hay fechas disponibles
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                Selecciona un período contable para continuar.
                            </p>
                        </div>
                    @endforelse
                </div>

                {{-- Pie del modal --}}
                <footer class="flex items-center justify-between gap-3
                               border-t border-slate-100 bg-slate-50
                               px-5 py-3">
                    <span class="text-[10px] text-slate-500">
                        Importes ilustrativos · No son datos reales
                    </span>

                    <button
                        type="button"
                        wire:click="closeModal"
                        class="rounded-lg border border-slate-200 bg-white
                               px-3 py-2 text-xs font-semibold text-slate-700
                               transition hover:bg-slate-100"
                    >
                        Cerrar
                    </button>
                </footer>
            </section>
        </div>
    @endif
</div>
