
{{-- resources/views/livewire/ventas/desglose.blade.php --}}

<div class="relative">
    <button
        type="button"
        wire:click="openModal"
        class="group flex w-full items-center justify-between gap-3 rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-left shadow-sm transition hover:border-fuchsia-300 hover:bg-fuchsia-50/40 focus:outline-none focus:ring-2 focus:ring-fuchsia-500/30"
    >
        <span class="flex min-w-0 items-center gap-3">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-fuchsia-50 text-fuchsia-700">
                <i class="bi bi-currency-exchange text-xl"></i>
            </span>

            <span class="min-w-0">
                <span class="block text-xs font-medium text-slate-500">
                    Desglose por monedas
                </span>

                <span class="block truncate text-sm font-semibold text-slate-800">
                    FerguMix · Demo
                </span>
            </span>
        </span>

        <svg
            class="h-5 w-5 shrink-0 text-slate-400 transition group-hover:text-fuchsia-600"
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
        <div
            x-transition.opacity
            class="fixed inset-0 z-[9999] flex items-center justify-center overflow-y-auto bg-slate-950/60 p-2 backdrop-blur-sm sm:p-5"
            wire:click.self="closeModal"
            @keydown.escape.window="$wire.closeModal()"
        >
            <section
                role="dialog"
                aria-modal="true"
                aria-labelledby="desglose-modal-title"
                class="flex max-h-[calc(100dvh-1rem)] w-full max-w-md flex-col overflow-hidden rounded-2xl border border-white/70 bg-white shadow-2xl shadow-slate-950/20 sm:max-h-[90vh] sm:rounded-3xl"
            >
                <header class="relative shrink-0 overflow-hidden border-b border-slate-200 bg-gradient-to-br from-fuchsia-50 via-white to-indigo-50 px-4 py-4 sm:px-6 sm:py-5">
                    <div class="pointer-events-none absolute -right-8 -top-12 h-36 w-36 rounded-full bg-fuchsia-100/60 blur-2xl"></div>

                    <div class="relative flex items-start gap-3">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-fuchsia-100 text-fuchsia-700 ring-1 ring-fuchsia-200/70">
                            <i class="bi bi-currency-exchange text-xl"></i>
                        </div>

                        <div class="min-w-0 flex-1">
                            <h2 id="desglose-modal-title" class="text-lg font-bold text-slate-900">
                                Desglose por moneda
                            </h2>

                            <p class="mt-1 text-xs leading-5 text-slate-600">
                                Distribuye el pago entre distintas monedas.
                                Los importes se convierten a la moneda principal.
                            </p>
                        </div>

                        <button
                            type="button"
                            wire:click="closeModal"
                            aria-label="Cerrar"
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-white/90 text-slate-500 transition hover:bg-white hover:text-slate-900"
                        >
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                </header>

                <div class="min-h-0 flex-1 space-y-4 overflow-y-auto px-3 py-4 sm:px-5">
                    <div class="flex items-center justify-between gap-3 rounded-xl bg-slate-50 px-3 py-3">
                        <div>
                            <p class="text-xs font-semibold text-slate-500">
                                Valor de la venta
                            </p>
                            <p class="mt-0.5 text-xs text-slate-400">
                                Importe expresado en moneda principal
                            </p>
                        </div>

                        <p class="text-lg font-bold tabular-nums text-slate-900">
                            {{ number_format((float) $valorVenta, 2, '.', ',') }}
                            {{ $primary['codigo'] ?? '' }}
                        </p>
                    </div>

                    @error('valorVenta')
                        <p class="text-xs text-red-600">{{ $message }}</p>
                    @enderror

                    @if ($primary)
                        <div class="space-y-2 border-b border-dashed border-slate-300 pb-4">
                            <div class="flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold {{ $primary['color_bg'] ?? 'bg-slate-100' }} {{ $primary['color_text'] ?? 'text-slate-800' }}">
                                        {{ $primary['codigo'] ?? 'Principal' }}
                                        <span class="ml-1 font-normal opacity-70">
                                            · Moneda principal
                                        </span>
                                    </span>

                                    <p class="mt-1 text-xs text-slate-500">
                                        Puedes modificar este importe sin alterar los demás.
                                    </p>
                                </div>

                                <span class="shrink-0 text-xs font-medium text-slate-500">
                                    Equivalencia: {{ $primary['eq_per_primary'] ?? 1 }}
                                </span>
                            </div>

                            <div class="flex items-center gap-2">
                                <input
                                    type="number"
                                    step="any"
                                    inputmode="decimal"
                                    wire:model.blur="primary.desglose"
                                    aria-label="Desglose de moneda principal"
                                    @class([
                                        'min-w-0 w-full rounded-xl border px-3 py-2.5 text-right text-sm tabular-nums',
                                        'border-red-400 focus:border-red-500 focus:ring-red-500'
                                            => $errors->has('primary.desglose'),
                                        'border-slate-300 focus:border-indigo-500 focus:ring-indigo-500'
                                            => !$errors->has('primary.desglose'),
                                    ])
                                >

                                <span class="shrink-0 rounded-full border border-dashed border-indigo-300 px-2 py-1 text-xs font-semibold tabular-nums text-indigo-600">
                                    {{ number_format((float) $this->importeDesglose((int) $primary['id']), 2, '.', ',') }}
                                </span>
                            </div>

                            @error('primary.desglose')
                                <p class="text-xs text-red-600">{{ $message }}</p>
                            @enderror

                            @if ($this->monedaPrincipalNegativa())
                                <div class="flex items-start gap-2 rounded-lg bg-amber-50 p-2.5 text-xs text-amber-800">
                                    <i class="bi bi-exclamation-triangle-fill mt-0.5"></i>
                                    <span>
                                        El desglose de la moneda principal es negativo.
                                        Revisa los importes de las demás monedas.
                                    </span>
                                </div>
                            @endif
                        </div>
                    @endif

                    @forelse ($others as $otherId => $it)
                        <div wire:key="desglose-moneda-{{ $otherId }}" class="space-y-2 border-b border-slate-100 pb-3">
                            <div class="flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold {{ $it['color_bg'] ?? 'bg-slate-100' }} {{ $it['color_text'] ?? 'text-slate-800' }}">
                                        {{ $it['codigo'] ?? 'Moneda' }}
                                    </span>

                                    <p class="mt-1 text-xs text-slate-500">
                                        Equivalencia en {{ $primary['codigo'] ?? '' }}:
                                        <span class="font-semibold tabular-nums">
                                            {{ $it['eq_per_primary'] ?? 1 }}
                                        </span>
                                    </p>
                                </div>

                                <span class="text-xs text-slate-400">
                                    Equivalente en {{ $primary['codigo'] ?? '' }}
                                </span>
                            </div>

                            <div class="flex items-center gap-2">
                                <input
                                    type="number"
                                    min="0"
                                    step="any"
                                    inputmode="decimal"
                                    wire:model.blur="others.{{ $otherId }}.desglose"
                                    aria-label="Desglose {{ $it['codigo'] ?? '' }}"
                                    @class([
                                        'min-w-0 w-full rounded-xl border px-3 py-2.5 text-right text-sm tabular-nums',
                                        'border-red-400 focus:border-red-500 focus:ring-red-500'
                                            => $errors->has("others.$otherId.desglose"),
                                        'border-slate-300 focus:border-indigo-500 focus:ring-indigo-500'
                                            => !$errors->has("others.$otherId.desglose"),
                                    ])
                                >

                                <span class="shrink-0 rounded-full border border-dashed border-indigo-300 px-2 py-1 text-xs font-semibold tabular-nums text-indigo-600">
                                    {{ number_format((float) $this->importeDesglose((int) $otherId), 2, '.', ',') }}
                                </span>
                            </div>

                            @error("others.$otherId.desglose")
                                <p class="text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    @empty
                        <p class="rounded-xl border border-dashed border-slate-200 px-3 py-4 text-center text-xs text-slate-500">
                            No hay otras monedas activas para desglosar.
                        </p>
                    @endforelse

                    <div class="space-y-3 rounded-xl border border-slate-200 bg-slate-50 p-3">
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-xs font-semibold text-slate-600">
                                Total del desglose
                            </span>

                            <span class="text-sm font-bold tabular-nums text-slate-900">
                                {{ number_format((float) $this->importeDesglose(), 2, '.', ',') }}
                                {{ $primary['codigo'] ?? '' }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between gap-3 border-t border-slate-200 pt-2">
                            <span class="text-xs text-slate-500">
                                Diferencia respecto a la venta
                            </span>

                            <span @class([
                                'text-sm font-bold tabular-nums',
                                'text-emerald-700' => $this->desgloseCoincide(),
                                'text-amber-700' => !$this->desgloseCoincide(),
                            ])>
                                {{ number_format((float) $this->diferenciaDesglose(), 2, '.', ',') }}
                                {{ $primary['codigo'] ?? '' }}
                            </span>
                        </div>

                        @if ($this->desgloseCoincide())
                            <p class="flex items-center gap-2 text-xs font-medium text-emerald-700">
                                <i class="bi bi-check-circle-fill"></i>
                                El desglose coincide con el valor de la venta.
                            </p>
                        @else
                            <p class="flex items-start gap-2 text-xs text-amber-800">
                                <i class="bi bi-exclamation-circle-fill mt-0.5"></i>
                                <span>
                                    El total del desglose no coincide con el valor
                                    de la venta. Ajusta los importes para cuadrarlo.
                                </span>
                            </p>
                        @endif
                    </div>
                </div>

                <footer class="flex shrink-0 justify-end border-t border-slate-200 bg-white px-4 py-3 sm:px-5">
                    <button
                        type="button"
                        wire:click="closeModal"
                        class="inline-flex items-center rounded-xl border border-slate-300 bg-white px-5 py-2 
                               justify-end gap-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
                    >
                        <i class="bi bi-x-lg"></i>
                        Cerrar
                    </button>
                </footer>
            </section>
        </div>
    @endif
</div>
