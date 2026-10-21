@php
    // Soporte a nombres de columnas: fecha_inicio/fin o fecha_inicial/final
    $fi = $fecha_inicio ?? $fecha_inicial ?? null;
    $ff = $fecha_fin    ?? $fecha_fin    ?? null;

    $fo = $fechaOperacion ?? $fechaOperacion ?? null;

@endphp

<div>
    <button
        type="button"
        wire:click="openModal"
        class="flex w-full items-center justify-between gap-2 rounded-xl border border-emerald-200 bg-emerald-50/60 px-3 py-2 text-xs md:text-sm font-medium text-slate-700 shadow-sm hover:bg-emerald-50 transition-colors"
    >
        <div class="flex items-center gap-1.5 min-w-0">
            <span class="text-emerald-600/100 font-normal">Fecha:</span>
            <span class="truncate text-emerald-700 font-semibold">
                @if($fo)
                    {{ $fo ? format_fecha($fo) : '—' }}
                @else
                    Definir fecha
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
                class="w-full max-w-sm rounded-2xl bg-white p-6 shadow-2xl"
            >
                <div class="mb-5 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-slate-800">
                        Seleccionar fecha
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
                    @foreach ($fechas_periodo as $fecha)
                        <button
                            type="button"
                            wire:click="seleccionarFechaOperacion('{{ $fecha['en'] }}')"
                            class="
                                flex w-full items-center justify-between
                                rounded-lg border px-4 py-3 text-left
                                transition
                                {{ $fechaOperacion === $fecha['en']
                                    ? 'border-indigo-500 bg-indigo-50 text-indigo-700'
                                    : 'border-slate-200 hover:bg-slate-50' }}
                            "
                        >
                            @php
                                // Soporte a nombres de columnas: fechaOperacion
                                $fo = $fecha['es'] ?? null;
                            @endphp
                            <span class="font-medium">
                                @if($fo)
                                    Dia:
                                    <strong>{{ $fo ? $fo : '—' }}</strong>
                                @endif
                            </span>

                            @if ($fechaOperacion === $fecha['en'])
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