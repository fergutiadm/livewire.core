        {{-- resources/views/modules/finanzas/denominacion/calculadora.blade.php --}}
        <div class="relative">
            @if(!$monedaFija)
                <button
                    type="button"
                    wire:click="openModal"
                    class="group flex w-full items-center justify-between gap-3 rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-left shadow-sm transition hover:border-purple-300 hover:bg-purple-50/40 focus:outline-none focus:ring-2 focus:ring-purple-500/30"
                >
                    <span class="flex min-w-0 items-center gap-3">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-purple-50 text-purple-700">
                            <i class="bi bi-currency-exchange text-xl"></i>
                        </span>

                        <span class="min-w-0">
                            <span class="block text-xs font-medium text-slate-500">
                                Calculadora por denominación
                            </span>

                            <span class="block truncate text-sm font-semibold text-slate-800">
                                FerguMix · Demo
                            </span>
                        </span>
                    </span>

                    <svg
                        class="h-5 w-5 shrink-0 text-slate-400 transition group-hover:text-purple-600"
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
            @endif

            @if($showModal)
                <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-800/75 p-4">
                    <div class="flex w-full max-w-2xl max-h-[90vh] flex-col overflow-hidden rounded-xl bg-white shadow-2xl">

                        {{-- HEADER --}}
                        <div class="flex shrink-0 items-center justify-between border-b border-gray-200 px-6 py-4">
                            <div class="min-w-0">
                                <h2 class="text-xl font-semibold text-gray-800">
                                    Calculadora por denominación de la moneda
                                </h2>

                                @if($monedaCodigo)
                                    <p class="mt-1 text-sm text-gray-500">
                                        <span class="text-xs font-semibold px-2 py-0.5">
                                            {{ $montoEsperado }}
                                        </span>
                                    </p>
                                @endif
                            </div>

                            <button
                                type="button"
                                wire:click="cerrar"
                                class="ml-4 shrink-0 text-2xl leading-none text-gray-400 transition hover:text-gray-700"
                                title="Cerrar"
                            >
                                &times;
                            </button>
                        </div>

                        {{-- CONTENIDO --}}
                        <div class="min-h-0 flex-1 overflow-y-auto px-6 py-6">

                            @if(!$monedaFija)
                                {{-- SELECTOR DE MONEDA --}}
                                <div class="mb-5">
                                    <label class="mb-2 block text-sm font-semibold text-gray-700">
                                        Moneda
                                    </label>

                                    <div class="flex flex-wrap gap-2">
                                        @foreach($monedas as $moneda)
                                            <button
                                                type="button"
                                                wire:key="calculadora-moneda-{{ $moneda['id'] }}"
                                                wire:click="cambiarMoneda({{ $moneda['id'] }})"
                                                @class([
                                                    'inline-flex items-center gap-2 rounded-lg border px-3 py-2 text-sm font-semibold transition',
                                                    'border-purple-500 bg-purple-50 ring-2 ring-purple-200' => $monedaId === (int) $moneda['id'],
                                                    'border-gray-200 bg-white hover:border-purple-300' => $monedaId !== (int) $moneda['id'],
                                                ])
                                            >
                                                <span class="rounded-full px-2 py-0.5 text-xs {{ $moneda['color_bg'] }} {{ $moneda['color_text'] }}">
                                                    {{ $moneda['codigo'] }}
                                                </span>

                                                <span class="text-gray-600">
                                                    {{ $moneda['nombre'] }}
                                                </span>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- DENOMINACIONES --}}
                            <section class="min-w-0 overflow-hidden rounded-lg border border-gray-200 bg-white">

                                <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
                                    <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-700">
                                        Monto por denominación
                                    </h3>
                                </div>

                                {{-- TOTAL SUPERIOR --}}
                                <div class="mt-4 flex items-center justify-between rounded-lg bg-gray-50 px-4 py-4">
                                    <span class="font-semibold text-gray-700">
                                        Total contado
                                    </span>

                                    <span class="text-lg font-bold tabular-nums text-gray-900">
                                        {{ number_format((float) $this->montoTotalContado(), 2, '.', ',') }}
                                        {{ $monedaCodigo }}
                                    </span>
                                </div>

                                <div class="space-y-2 p-4">
                                    @forelse($cards as $denominacionId => $card)
                                        <div
                                            wire:key="denominaciones-moneda-{{ $monedaId }}-card-{{ $denominacionId }}"
                                            class="w-full min-w-0 rounded-lg border border-gray-200 bg-white p-2 shadow-sm transition"
                                        >
                                            <div class="grid grid-cols-3 items-center gap-2">

                                                <div class="flex items-center justify-end px-2">
                                                    @php
                                                        $color_bg = $card['color_bg'] ?? 'text-slate-300';
                                                        $color_text = $card['color_text'] ?? 'text-slate-500';
                                                    @endphp

                                                    <span class="rounded-lg border border-dashed border-gray-500 px-2 py-0.5 text-sm font-semibold {{ $color_text }} {{ $color_bg }}">
                                                        {{ $card['denominacion'] }}
                                                    </span>
                                                </div>

                                                <div class="flex items-center justify-center px-2">
                                                    <x-input-clearable
                                                        type="text"
                                                        wire:model.blur="cards.{{ $denominacionId }}.cantidad_billetes"
                                                        wire:change="actualizarCantidadBilletes({{ $denominacionId }})"
                                                        class="w-full rounded-xl border-slate-300 font-medium"
                                                        placeholder="Cantidad de billetes"
                                                    />
                                                </div>

                                                <div class="px-2 text-right">
                                                    <span class="text-sm font-semibold tabular-nums text-gray-800">
                                                        {{ number_format((float) $this->importeDenominacion($denominacionId), 2, '.', ',') }}
                                                        {{ $monedaCodigo }}
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="grid grid-cols-1 items-center gap-2 md:grid-cols-3">
                                                <div class="col-start-2">
                                                    @error("cards.$denominacionId.cantidad_billetes")
                                                        <p class="mt-1 text-xs text-red-600">
                                                            {{ $message }}
                                                        </p>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="rounded-lg border border-dashed border-gray-300 p-6 text-center text-sm text-gray-500">
                                            Sin denominaciones para esta moneda.
                                        </div>
                                    @endforelse
                                </div>

                                {{-- TOTAL INFERIOR --}}
                                <div class="my-4 flex items-center justify-between rounded-lg bg-gray-50 px-4 py-4">
                                    <span class="font-semibold text-gray-700">
                                        Total contado
                                    </span>

                                    <span class="text-lg font-bold tabular-nums text-gray-900">
                                        {{ number_format((float) $this->montoTotalContado(), 2, '.', ',') }}
                                        {{ $monedaCodigo }}
                                    </span>
                                </div>

                            </section>
                        </div>

                        {{-- FOOTER --}}
                        <div class="flex shrink-0 items-center justify-end gap-3 border-t border-gray-200 bg-white px-6 py-4">

                            <button
                                type="button"
                                wire:click="cerrar"
                                class="btn-secondary justify-center gap-2"
                            >
                                <i class="bi bi-x-lg"></i>
                                Cerrar
                            </button>

                            <button
                                type="button"
                                wire:click="guardar"
                                @disabled(!$this->hayCambios())
                                @class([
                                    'btn-primary justify-center gap-2',
                                    'opacity-50 cursor-not-allowed' => !$this->hayCambios(),
                                ])
                            >
                                <i class="bi bi-floppy"></i>
                                Aplicar
                            </button>

                        </div>
                    </div>
                </div>
            @endif
        </div>
