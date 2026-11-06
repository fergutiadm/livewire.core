{{-- resources/views/resumenes/tarjetas.blade.php --}}

<div
    x-data="{
        loading: false,
        loadingMessage: '',
        mostrarResumenes: false
    }"
    x-on:loading-start.window="loading = true; loadingMessage = $event.detail.message ?? 'Cargando...';"
    x-on:loading-stop.window="loading = false; loadingMessage = '';"
    x-on:media-procesada.window="loading = false; loadingMessage = '';"
    x-init="
        Livewire.hook('request', ({ fail }) => {
            fail(() => { loading = false; loadingMessage = ''; });
        });
        Livewire.hook('commit', ({ succeed }) => {
            succeed(() => { loading = false; loadingMessage = ''; });
        });
    "
    class="relative"
>

    {{-- Acceso al resumen --}}
    <button
        type="button"
        wire:click="openModal"
        class="group flex w-full items-center justify-between gap-3
               rounded-xl border border-slate-200 bg-white px-3 py-2.5
               text-left shadow-sm transition
               hover:border-orange-300 hover:bg-orange-50/40
               focus:outline-none focus:ring-2 focus:ring-orange-500/30"
    >
        <span class="flex min-w-0 items-center gap-3">
            <span
                class="flex h-10 w-10 shrink-0 items-center justify-center
                       rounded-xl bg-orange-50 text-orange-700"
            >
                <i class="h-5 w-5 bi bi-info-circle"></i>
                {{-- <svg
                    class="h-5 w-5"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    aria-hidden="true"
                >
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M2 21v-2a4 4 0 0 1 4-4h6a4 4 0 0 1 4 4v2"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                </svg> --}}
            </span>
                
            <span class="min-w-0">
                <span class="block text-xs font-medium text-slate-500">
                    Tarjetas Magneticas
                </span>
                
                <span class="block truncate text-sm font-semibold text-slate-800">
                    {{ count($grupoTarjetas) }} · Demo
                </span>
            </span>
        </span>

        <svg
            class="h-5 w-5 shrink-0 text-slate-400 transition
                group-hover:text-orange-600"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.8"
            stroke-linecap="round"
            stroke-linejoin="round"
            aria-hidden="true"
        >
            <path d="m7 10 5 5 5-5"/>
        </svg>
    </button>

    @if ($showModal)
        {{-- Fondo del modal --}}
        <div
            x-transition.opacity
            class="fixed inset-0 z-[9999] flex items-center justify-center
                   overflow-y-auto bg-slate-950/60 p-2 backdrop-blur-sm
                   sm:p-5"
            wire:click.self="closeModal"
            @keydown.escape.window="$wire.closeModal()"
        >
            {{-- Contenedor principal --}}
            <section
                role="dialog"
                aria-modal="true"
                aria-labelledby="tarjetas-modal-title"
                class="flex max-h-[calc(100dvh-1rem)] w-full max-w-5xl
                       flex-col overflow-hidden rounded-2xl border
                       border-white/70 bg-white shadow-2xl
                       shadow-slate-950/20 sm:max-h-[90vh] sm:rounded-3xl"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-3 scale-[0.98]"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            >
                {{-- Encabezado --}}
                <header
                    class="relative shrink-0 overflow-hidden border-b
                           border-slate-200 bg-gradient-to-br
                           from-orange-50 via-white to-indigo-50
                           px-4 py-4 sm:px-7 sm:py-6"
                >
                    <div
                        class="pointer-events-none absolute -right-8 -top-12
                               h-36 w-36 rounded-full bg-orange-100/60 blur-2xl"
                    ></div>

                    <div class="relative flex min-w-0 items-start gap-3 sm:gap-4">
                        <div
                            class="flex h-10 w-10 shrink-0 items-center
                                   justify-center rounded-xl bg-orange-100
                                   text-orange-700 ring-1 ring-orange-200/70
                                   sm:h-12 sm:w-12 sm:rounded-2xl"
                        >
                            <svg
                                class="h-5 w-5 sm:h-6 sm:w-6"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.7"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                aria-hidden="true"
                            >
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M2 21v-2a4 4 0 0 1 4-4h6a4 4 0 0 1 4 4v2"/>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                            </svg>
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <h2
                                    id="tarjetas-modal-title"
                                    class="text-base font-bold leading-tight
                                           tracking-tight text-slate-900
                                           sm:text-xl"
                                >
                                    Resumen de tarjetas
                                </h2>

                                <span
                                    class="inline-flex shrink-0 items-center gap-1.5
                                           rounded-full border border-amber-200
                                           bg-amber-50 px-2 py-1 text-[9px]
                                           font-bold uppercase tracking-wide
                                           text-amber-700 sm:text-[10px]"
                                >
                                    <span
                                        class="h-1.5 w-1.5 rounded-full bg-amber-500"
                                    ></span>
                                    Demo
                                </span>
                            </div>

                            <p class="mt-1 text-xs leading-5 text-slate-600 sm:text-sm">
                                Monto diario y acumulado por tarjeta.
                            </p>

                            
                            <div class="mt-3 flex flex-col gap-2 text-xs text-slate-500 sm:flex-row sm:flex-wrap sm:items-center sm:gap-x-4">
                                @if ($periodoSeleccionado)
                                    <span class="inline-flex min-w-0 items-center gap-1.5">
                                        <svg
                                            class="h-4 w-4 shrink-0"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.7"
                                            aria-hidden="true"
                                        >
                                            <rect x="3" y="5" width="18" height="16" rx="2"/>
                                            <path d="M16 3v4M8 3v4M3 10h18"/>
                                        </svg>

                                        <span>
                                            Período:
                                            {{ $periodoSeleccionado->fecha_inicio->format('d/m/Y') }}
                                            –
                                            {{ $periodoSeleccionado->fecha_fin->format('d/m/Y') }}
                                        </span>
                                    </span>

                                    <span class="inline-flex min-w-0 items-center gap-1.5">
                                        <svg
                                            class="h-4 w-4 shrink-0"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.7"
                                            aria-hidden="true"
                                        >
                                            <circle cx="12" cy="12" r="9"/>
                                            <path d="M12 7v5l3 2"/>
                                        </svg>

                                        <span>
                                            Fecha:
                                            {{ \Carbon\Carbon::parse($fechaOperacion)->format('d/m/Y') }}
                                        </span>
                                    </span>
                                @else
                                    <span class="text-amber-700">
                                        Selecciona un período contable válido.
                                    </span>
                                @endif
                            </div>

                        </div>

                        <button
                            type="button"
                            wire:click="closeModal"
                            aria-label="Cerrar resumen de tarjetas"
                            class="flex h-10 w-10 shrink-0 items-center
                                   justify-center rounded-xl border
                                   border-slate-200 bg-white/90 text-slate-500
                                   transition hover:border-slate-300
                                   hover:bg-white hover:text-slate-900
                                   focus:outline-none focus:ring-2
                                   focus:ring-orange-500/40"
                        >
                            <svg
                                class="h-5 w-5"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                aria-hidden="true"
                            >
                                <path d="m18 6-12 12M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </header>

                {{-- Contenido desplazable --}}
                <div class="min-h-0 flex-1 overflow-y-auto overscroll-contain px-4 py-4 sm:px-7 sm:py-5">
                    <div class="mb-4 flex flex-wrap items-end justify-between gap-2">
                        <div class="min-w-0">
                            <h3 class="text-sm font-bold text-slate-800">
                                Detalle por tarjeta
                            </h3>
                            <p class="mt-1 text-xs leading-5 text-slate-500">
                                Comparación del día con el acumulado del período.
                            </p>
                        </div>

                        <span class="text-xs text-slate-400">
                            {{ count($grupoTarjetas) }} registros de prueba
                        </span>
                    </div>

                    <div class="grid grid-cols-1 gap-3 lg:grid-cols-2">
                        @foreach ($grupoTarjetas as $tarjeta)
                            <article
                                wire:key="trabajador-demo-{{ $tarjeta['id'] }}"
                                class="min-w-0 rounded-2xl border border-slate-200
                                       bg-white p-3.5 transition duration-200
                                       hover:border-orange-200 hover:shadow-md
                                       hover:shadow-slate-200/50 sm:p-4"
                            >
                                {{-- Identidad --}}
                                <div class="flex min-w-0 items-center gap-3">
                                    <div
                                        class="flex h-11 w-11 shrink-0 items-center
                                               justify-center rounded-xl
                                               bg-gradient-to-br from-orange-100
                                               to-indigo-100 text-sm font-bold
                                               text-orange-800 ring-1
                                               ring-inset ring-orange-200/60"
                                    >
                                        {{ collect(explode(' ', trim($tarjeta['nombre'])))
                                            ->filter()
                                            ->take(2)
                                            ->map(fn ($parte) => mb_substr($parte, 0, 1))
                                            ->implode('') }}
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <h4 class="truncate text-sm font-bold text-slate-900">
                                            {{ $tarjeta['nombre'] }}
                                        </h4>
                                        <p class="mt-0.5 text-xs text-slate-500">
                                            Numero: {{ $tarjeta['numero'] }}
                                        </p>
                                    </div>

                                    <span
                                        class="shrink-0 rounded-lg bg-slate-100
                                               px-2 py-1 text-[10px] font-semibold
                                               uppercase tracking-wide text-slate-500"
                                    >
                                        Demo
                                    </span>
                                </div>

                                {{-- Monto --}}
                                <div
                                    class="mt-4 rounded-xl border border-orange-100
                                           bg-gradient-to-r from-orange-50/80
                                           to-white p-3 sm:p-3.5"
                                >
                                    <p class="text-xs font-medium text-orange-800">
                                        Monto del día
                                    </p>

                                    <div class="mt-1 flex min-w-0 flex-wrap items-baseline gap-x-2 gap-y-0.5">
                                        <p
                                            class="min-w-0 break-words text-xl
                                                   font-bold tracking-tight
                                                   tabular-nums text-slate-900
                                                   sm:text-2xl"
                                        >
                                            {{ number_format((float) $tarjeta['monto'], 2, ',', '.') }}
                                        </p>
                                        <span class="text-xs font-semibold text-slate-500">
                                            {{ $tarjeta['moneda'] }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Indicadores secundarios --}}
                                <div class="mt-3 grid grid-cols-2 gap-2.5 sm:gap-3">
                                    <div
                                        class="min-w-0 rounded-xl border
                                               border-slate-100 bg-slate-50/50
                                               p-2.5 sm:p-3"
                                    >
                                        <p class="text-[11px] leading-4 text-slate-500">
                                            Monto Acumulado
                                        </p>

                                        <p
                                            class="mt-1 break-words text-sm
                                                   font-bold tabular-nums
                                                   text-slate-900"
                                        >
                                            {{ number_format((float) $tarjeta['monto_acumulado'], 2, ',', '.') }}
                                        </p>

                                        <span class="text-[10px] font-medium text-slate-400">
                                            {{ $tarjeta['moneda'] }} · período
                                        </span>
                                    </div>

                                    <div
                                        class="min-w-0 rounded-xl border
                                               border-emerald-100 bg-emerald-50/70
                                               p-2.5 sm:p-3"
                                    >
                                        <p class="text-[11px] leading-4 text-emerald-800">
                                            Monto
                                        </p>

                                        <p
                                            class="mt-1 break-words text-sm
                                                   font-bold tabular-nums
                                                   text-slate-800"
                                        >
                                            {{ number_format((float) $tarjeta['monto_total'], 2, ',', '.') }}
                                        </p>

                                        <span class="text-[10px] font-medium text-slate-500">
                                            {{ $tarjeta['moneda'] }} · total
                                        </span>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>

                {{-- Pie --}}
                <footer
                    class="flex shrink-0 flex-col gap-3 border-t
                           border-slate-200 bg-white px-4 py-3
                           sm:flex-row sm:items-center sm:justify-between
                           sm:px-7 sm:py-4"
                >
                    <p class="text-xs leading-5 text-slate-500">
                        Los datos son ficticios. No representan cálculos ni registros reales.
                    </p>

                    <button
                        type="button"
                        wire:click="closeModal"
                        class="inline-flex min-h-11 w-full shrink-0
                               items-center justify-center gap-2 rounded-xl
                               bg-slate-900 px-5 py-2.5 text-sm font-semibold
                               text-white shadow-sm transition
                               hover:bg-slate-700 focus:outline-none
                               focus:ring-2 focus:ring-slate-500/40
                               focus:ring-offset-2 sm:w-auto"
                    >
                        <i class="bi bi-x-lg"></i>
                        Cerrar resumen
                    </button>
                </footer>
            </section>
        </div>
    @endif
</div>