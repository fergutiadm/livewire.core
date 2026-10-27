{{-- resources/views/navigation/periodo-selector.blade.php --}}
<div>
    {{-- Selector global del período actual --}}
    <button
        type="button"
        wire:click="openModal"
        class="flex w-full items-center justify-between gap-2 rounded-xl border border-emerald-200 bg-emerald-50/60 px-3 py-2 text-xs font-medium text-slate-700 shadow-sm transition-colors hover:bg-emerald-50 md:text-sm"
    >
        <span class="flex min-w-0 items-center gap-1.5">
            <span class="shrink-0 text-emerald-600/80">Período:</span>

            <span class="truncate font-semibold text-emerald-950">
                @if($fecha_inicio || $fecha_fin)
                    @if($periodoSelectedActivo)
                        <i
                            class="bi bi-check-circle-fill mr-1 text-green-600"
                            title="Período activo"
                        ></i>
                    @endif

                    {{ $fecha_inicio ? format_fecha($fecha_inicio) : '—' }}
                    –
                    {{ $fecha_fin ? format_fecha($fecha_fin) : '—' }}
                @else
                    Definir período
                @endif
            </span>
        </span>

        <svg
            class="h-4 w-4 shrink-0 text-emerald-600/70"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="m19 9-7 7-7-7"
            />
        </svg>
    </button>

    @if($showModal)
        <div
            class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/40 px-4 py-6"
            wire:click.self="closeModal"
        >
            <div
                class="flex max-h-full w-full max-w-lg flex-col overflow-hidden rounded-2xl bg-white shadow-2xl"
            >
                {{-- Encabezado --}}
                <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-800">
                            Seleccionar período
                        </h2>

                        <p class="mt-0.5 text-xs text-slate-500">
                            Filtra por año y mes antes de seleccionar.
                        </p>
                    </div>

                    <button
                        type="button"
                        wire:click="closeModal"
                        class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
                        aria-label="Cerrar"
                    >
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                {{-- Filtros --}}
                <div class="border-b border-slate-100 bg-slate-50/80 px-5 py-4">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label
                                for="year_selector"
                                class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500"
                            >
                                <i class="bi bi-calendar3 mr-1"></i>
                                Año
                            </label>

                            <select
                                id="year_selector"
                                wire:model.live="year_selector"
                                class="block w-full rounded-xl border-slate-200 bg-white py-2.5 pl-3 pr-8 text-sm font-semibold text-slate-700 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                @foreach($datosPeriodo['anios'] ?? [] as $anio)
                                    <option value="{{ $anio }}">
                                        {{ $anio }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label
                                for="month_selector"
                                class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500"
                            >
                                <i class="bi bi-calendar-month mr-1"></i>
                                Mes
                            </label>

                            <select
                                id="month_selector"
                                wire:model.live="month_selector"
                                class="block w-full rounded-xl border-slate-200 bg-white py-2.5 pl-3 pr-8 text-sm text-slate-700 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                @disabled(empty($datosPeriodo['meses']))
                            >
                                @foreach($datosPeriodo['meses'] ?? [] as $numeroMes => $nombreMes)
                                    <option value="{{ $numeroMes }}">
                                        {{ $nombreMes }}
                                    </option>
                                @endforeach
                            </select>

                            @if(empty($datosPeriodo['meses']))
                                <p class="mt-1 text-xs text-amber-600">
                                    No hay meses disponibles para este año.
                                </p>
                            @endif
                        </div>

                    </div>

                    <p class="mt-2 text-xs text-slate-500">
                        <i class="bi bi-info-circle mr-1"></i>
                        Se muestran períodos que comienzan o terminan en el año y mes seleccionados.
                    </p>
                </div>

                {{-- Lista de períodos --}}
                <div class="min-h-0 flex-1 overflow-y-auto px-5 py-4">
                    <div class="mb-3 flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-slate-700">
                            Períodos disponibles
                        </h3>

                        <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-500">
                            {{ $periodos->count() }}
                        </span>
                    </div>

                    @forelse($periodos as $periodo)
                        @php
                            $fi = $periodo->fecha_inicio ?? $periodo->fecha_inicial ?? null;
                            $ff = $periodo->fecha_fin ?? $periodo->fecha_final ?? null;
                            $seleccionado = (int) $periodoId === (int) $periodo->id;
                        @endphp

                        <button
                            type="button"
                            wire:key="periodo-selector-{{ $periodo->id }}"
                            wire:click="seleccionarPeriodo({{ $periodo->id }})"
                            @class([
                                'flex w-full items-center justify-between gap-3 rounded-xl border px-4 py-3 text-left transition',
                                'border-indigo-300 bg-indigo-50 ring-1 ring-indigo-200' => $seleccionado,
                                'border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50' => !$seleccionado,
                            ])
                        >
                            <span class="min-w-0">
                                <span class="flex flex-wrap items-center gap-2">
                                    <span class="text-sm font-semibold text-slate-800">
                                        {{ $fi ? format_fecha($fi) : '—' }}
                                        –
                                        {{ $ff ? format_fecha($ff) : '—' }}
                                    </span>

                                    @if($periodo->activo)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-green-100 px-2 py-0.5 text-[10px] font-semibold text-green-700">
                                            <i class="bi bi-check-circle-fill"></i>
                                            Activo
                                        </span>
                                    @endif
                                </span>
                            </span>

                            @if($seleccionado)
                                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-indigo-600 text-white">
                                    <i class="bi bi-check-lg"></i>
                                </span>
                            @else
                                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-slate-300">
                                    <i class="bi bi-chevron-right"></i>
                                </span>
                            @endif
                        </button>

                        <details
                            wire:key="resumen-periodo-{{ $periodo->id }}"
                            class="group mt-1 mb-2 overflow-hidden rounded-xl border border-slate-200/80 bg-slate-50/70 transition-colors open:border-indigo-200 open:bg-white"
                        >
                            <summary class="flex cursor-pointer list-none items-center justify-between gap-3 px-3 py-2.5 hover:bg-slate-100/70 [&::-webkit-details-marker]:hidden">
                                <span class="flex min-w-0 items-center gap-2">
                                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600">
                                        <i class="bi bi-bar-chart-line"></i>
                                    </span>

                                    <span class="min-w-0">
                                        <span class="block text-xs font-semibold text-slate-700">
                                            Resumen económico
                                        </span>
                                        <span class="block text-[11px] text-slate-400">
                                            Ventas, gastos y ganancia
                                        </span>
                                    </span>
                                </span>

                                <i class="bi bi-chevron-down shrink-0 text-xs text-slate-400 transition-transform group-open:rotate-180"></i>
                            </summary>

                            <div class="grid grid-cols-3 gap-2 border-t border-slate-100 p-3">
                                <div class="min-w-0 rounded-lg bg-indigo-50/70 p-2">
                                    <span class="block text-[10px] font-medium uppercase tracking-wide text-indigo-500">
                                        Ventas
                                    </span>
                                    <span class="mt-1 block break-words text-sm font-bold leading-5 text-indigo-900">
                                        1 234 560
                                    </span>
                                    <span class="text-[10px] text-indigo-400">CUP</span>
                                </div>

                                <div class="min-w-0 rounded-lg bg-amber-50/70 p-2">
                                    <span class="block text-[10px] font-medium uppercase tracking-wide text-amber-600">
                                        Gastos
                                    </span>
                                    <span class="mt-1 block break-words text-sm font-bold leading-5 text-amber-900">
                                        25 000
                                    </span>
                                    <span class="text-[10px] text-amber-500">CUP</span>
                                </div>

                                <div class="min-w-0 rounded-lg bg-emerald-50/70 p-2">
                                    <span class="block text-[10px] font-medium uppercase tracking-wide text-emerald-600">
                                        Ganancia
                                    </span>
                                    <span class="mt-1 block break-words text-sm font-bold leading-5 text-emerald-900">
                                        1 209 560
                                    </span>
                                    <span class="text-[10px] text-emerald-500">CUP</span>
                                </div>
                            </div>
                        </details>

                    @empty
                        <div class="rounded-xl border border-dashed border-slate-300 px-4 py-10 text-center">
                            <i class="bi bi-calendar2-x text-3xl text-slate-300"></i>

                            <p class="mt-3 text-sm font-medium text-slate-600">
                                No hay períodos disponibles
                            </p>

                            <p class="mt-1 text-xs text-slate-400">
                                Prueba con otro año o selecciona todos los meses.
                            </p>
                        </div>
                    @endforelse
                </div>

                {{-- Pie --}}
                <div class="flex items-center justify-between border-t border-slate-100 bg-slate-50 px-5 py-3">
                    <span class="text-xs text-slate-500">
                        {{ $periodos->count() === 1 ? '1 período encontrado' : $periodos->count() . ' períodos encontrados' }}
                    </span>

                    <button
                        type="button"
                        wire:click="closeModal"
                        class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-600 shadow-sm transition hover:bg-slate-100"
                    >
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
