<div>
    <button
        type="button"
        wire:click="openModal"
        class="flex w-full items-center justify-between gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs md:text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 transition-colors"
    >
        <div class="flex items-center gap-1.5 min-w-0">
            <span class="text-slate-400 font-normal">Local:</span>
            <span class="truncate font-semibold text-slate-800">
                {{ $localActual?->nombre ?? 'Seleccionar' }}
            </span>
        </div>
        <svg class="h-4 w-4 flex-shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"/>
        </svg>
    </button>

    @if ($showModal)
        <div
            class="fixed inset-0 z-[9999] flex items-center
                   justify-center overflow-y-auto bg-slate-950/60
                   p-2 backdrop-blur-sm sm:p-5"
            wire:click.self="closeModal"
        >
            <div
                role="dialog"
                aria-modal="true"
                aria-labelledby="local-modal-title"
                class="flex max-h-[calc(100dvh-1rem)] w-full max-w-md
                    flex-col overflow-hidden rounded-2xl
                    border border-white/70 bg-white
                    shadow-2xl shadow-slate-950/20
                    sm:max-h-[90vh] sm:rounded-3xl"
            >
                {{-- ============================================================
                    ENCABEZADO DEL MODAL
                ============================================================ --}}
                <header
                    class="relative shrink-0 overflow-hidden border-b
                        border-slate-200 bg-gradient-to-br
                        from-lime-50 via-white to-purple-50
                        px-4 py-4 sm:px-6 sm:py-5"
                >
                    {{-- Efecto decorativo --}}
                    <div
                        class="pointer-events-none absolute -right-8 -top-12
                            h-36 w-36 rounded-full
                            bg-lime-100/60 blur-2xl"
                    ></div>

                    <div class="relative flex items-start gap-3">

                        {{-- Icono --}}
                        <div
                            class="flex h-11 w-11 shrink-0 items-center
                                justify-center rounded-xl
                                bg-lime-100 text-lime-700
                                ring-1 ring-lime-200/70"
                        >
                            <i class="bi bi-building text-xl"></i>
                        </div>

                        {{-- Título y descripción --}}
                        <div class="min-w-0 flex-1">

                            <h2
                                id="local-modal-title"
                                class="text-lg font-bold text-slate-900"
                            >
                                Seleccionar local
                            </h2>

                        </div>

                        {{-- Botón cerrar --}}
                        <button
                            type="button"
                            wire:click="closeModal"
                            aria-label="Cerrar"
                            class="flex h-10 w-10 shrink-0 items-center
                                justify-center rounded-xl
                                border border-slate-200 bg-white/90
                                text-slate-500 transition
                                hover:border-slate-300 hover:bg-white
                                hover:text-slate-900
                                focus:outline-none focus:ring-2
                                focus:ring-lime-500/40"
                        >
                            <i class="bi bi-x-lg"></i>
                        </button>

                    </div>
                </header>

                <div class="space-y-2 px-3 py-5">
                    @foreach ($locales as $local)
                        @php
                            $seleccionado = (int) $localId === (int) $local->id;
                        @endphp
                        <button
                            type="button"
                            wire:click="seleccionarLocal({{ $local->id }})"
                            
                            @class([
                                'flex w-full items-center justify-between gap-3 rounded-xl border px-4 py-3 text-left transition',
                                'border-purple-300 bg-purple-50 ring-1 ring-purple-200' => $seleccionado,
                                'border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50' => !$seleccionado,
                            ])
                        >
                            <span class="font-medium">
                                {{ $local->nombre }}
                            </span>

                            @if($seleccionado)
                                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-purple-600 text-white">
                                    <i class="bi bi-check-lg"></i>
                                </span>
                            @else
                                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-slate-300">
                                    <i class="bi bi-chevron-right"></i>
                                </span>
                            @endif
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>