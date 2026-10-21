@php
    // Soporte a nombres de columnas: fecha_inicio/fin o fecha_inicial/final
    $fi = $fecha_inicio ?? $fecha_inicial ?? null;
    $ff = $fecha_fin    ?? $fecha_fin    ?? null;

@endphp

<div>
    <button
        type="button"
        wire:click="openModal"
        class="flex w-full items-center justify-between gap-2 rounded-xl border border-emerald-200 bg-emerald-50/60 px-3 py-2 text-xs md:text-sm font-medium text-slate-700 shadow-sm hover:bg-emerald-50 transition-colors"
    >
        <div class="flex items-center gap-1.5 min-w-0">
            <span class="text-emerald-600/80 font-normal">Periodo:</span>
            <span class="truncate text-emerald-950 font-semibold">
                @if($fi || $ff)
                    @if($periodoSelectedActivo)
                        <i class="bi bi-check-circle-fill text-green-600"
                        title="Periodo activo"></i>
                    @endif
                    {{ $fi ? format_fecha($fi) : '—' }} – {{ $ff ? format_fecha($ff) : '—' }}
                @else
                    Definir periodo
                @endif
            </span>
        </div>
        <svg class="h-4 w-4 flex-shrink-0 text-emerald-600/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"/>
        </svg>
    </button>

    @if ($showModal)
        <div
            class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/40 px-4"
            wire:click.self="closeModal"
        >
            <div
                class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl"
            >
                <div class="mb-5 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-slate-800">
                        Seleccionar periodo
                    </h2>

                    <button
                        type="button"
                        wire:click="closeModal"
                        class="text-slate-400 hover:text-slate-600"
                    >
                        ✕
                    </button>
                </div>

                <div class="space-y-2">
                    @foreach ($periodos as $periodo)
                        <button
                            type="button"
                            wire:click="seleccionarPeriodo({{ $periodo->id }})"
                            class="
                                flex w-full items-center justify-between
                                rounded-lg border px-4 py-3 text-left
                                transition
                                {{ $periodoId === $periodo->id
                                    ? 'border-indigo-500 bg-indigo-50 text-indigo-700'
                                    : 'border-slate-200 hover:bg-slate-50' }}
                            "
                        >
                            @php
                                // Soporte a nombres de columnas: fecha_inicio/fin o fecha_inicial/final
                                $fi = $periodo->fecha_inicio ?? $periodo->fecha_inicial ?? null;
                                $ff = $periodo->fecha_fin    ?? $periodo->fecha_fin    ?? null;
                            @endphp
                            <span class="font-medium">
                                @if($fi || $ff)
                                    Rango: 
                                    @if($periodo->activo)
                                        <i class="bi bi-check-circle-fill text-green-600"
                                        title="Periodo activo"></i>
                                    @endif
                                    <strong>{{ $fi ? format_fecha($fi) : '—' }}</strong>
                                    – <strong>{{ $ff ? format_fecha($ff) : '—' }}</strong>
                                @endif
                            </span>

                            @if ($periodoId === $periodo->id)
                                <span class="text-sm font-semibold">
                                    ✓
                                </span>
                            @endif
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>