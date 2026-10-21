    {{-- resources/views/modules/finanzas/cierre-caja/detalles-manager.blade.php --}}
    <div>

        @if($show)

            {{-- ============================================================
                OVERLAY DEL MODAL
                ============================================================ --}}
            <div
                class="fixed inset-0 z-50 flex items-center justify-center bg-gray-800/75 p-4"
            >

                {{-- ========================================================
                    MODAL
                    ======================================================== --}}
                <div
                    class="flex w-full max-w-2xl max-h-[90vh] flex-col overflow-hidden rounded-xl bg-white shadow-2xl"
                >

                    {{-- ====================================================
                        HEADER
                        ==================================================== --}}
                    <div
                        class="flex shrink-0 items-center justify-between border-b border-gray-200 px-6 py-4"
                    >
                        <div class="min-w-0">

                            <h2 class="text-xl font-semibold text-gray-800">
                                Editar detalles de la moneda
                            </h2>

                            @if($cajaNombre)
                                <p class="mt-1 text-sm text-gray-500">
                                    <span>Caja: <span class="font-semibold">{{ $cajaNombre }}</span></span>
                                    @if($monedaCodigo)
                                            <span
                                                class="text-xs font-semibold px-2 py-0.5">
                                                {{ $montoEsperado }}
                                            </span>
                                            <span
                                                class="text-xs font-semibold px-2 py-0.5 rounded-full ml-2 flex-shrink-0
                                                {{ $monedaColorBg }}
                                                {{ $monedaColorText }}">
                                                {{ $monedaCodigo }}
                                            </span>
                                    @endif
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


                    {{-- ====================================================
                        CONTENIDO
                        ==================================================== --}}
                    <div
                        class="min-h-0 flex-1 overflow-y-auto px-6 py-6"
                    >

                        <div>

                            {{-- ============================================
                                CANTIDAD POR DENOMINACION DE MONEDA
                                ============================================ --}}
                            <section
                                class="min-w-0 overflow-hidden rounded-lg border border-gray-200 bg-white"
                            >

                                <div
                                    class="border-b border-gray-200 bg-gray-50 px-4 py-3"
                                >
                                    <h3
                                        class="text-sm font-semibold uppercase tracking-wide text-gray-700"
                                    >
                                        Monto por Denominaci&oacute;n
                                    </h3>
                                </div>

                                <div class="mt-4 flex items-center justify-between rounded-lg bg-gray-50 px-4 py-4">
                                    <span class="font-semibold text-gray-700">
                                        Total contado
                                    </span>

                                    <span class="text-lg font-bold tabular-nums text-gray-900">
                                        {{ number_format(
                                            (float) $this->montoTotalContado(),
                                            2,
                                            '.',
                                            ','
                                        ) }}
                                        {{ $monedaCodigo }}
                                    </span>
                                </div>


                                <div class="space-y-2 p-4">

                                    @forelse($cards as $denominacionId => $card)

                                        {{-- ==================================================
                                            CARD DE LA MONEDA
                                            ================================================== --}}
                                        <div
                                            wire:key="denominaciones-moneda-card-{{ $denominacionId }}"

                                            class="w-full min-w-0 rounded-lg border border-gray-200 bg-white p-2 shadow-sm transition"
                                        >

                                            {{-- ==========================================
                                                CABECERA ---
                                                ========================================== --}}
                                            {{-- <div
                                            class="flex items-center justify-between gap-3"
                                            > --}}
                                                <div
                                                    class="grid grid-cols-1 md:grid-cols-3 items-center gap-2"
                                                >

                                                    <div class="flex items-center justify-end px-2">
                                                        <span
                                                            class="font-semibold text-gray-800"
                                                        >
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

                                                    <div class="text-right px-2">
                                                        <span class="font-semibold tabular-nums text-gray-800">
                                                            {{ number_format(
                                                                (float) $this->importeDenominacion($denominacionId),
                                                                2,
                                                                '.',
                                                                ','
                                                            ) }}
                                                            {{ $monedaCodigo }}
                                                        </span>
                                                    </div>

                                                </div>
                                                
                                                <div
                                                    class="grid grid-cols-1 md:grid-cols-3 items-center gap-2"
                                                >
                                                    <div class="col-start-2">
                                                        @error("cards.$denominacionId.cantidad_billetes")
                                                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                                        @enderror
                                                    </div>
                                                </div>

                                            {{-- </div> --}}

                                        </div>

                                    @empty

                                        <div
                                            class="rounded-lg border border-dashed border-gray-300 p-6 text-center text-sm text-gray-500"
                                        >
                                            Sin denominaciones por moneda
                                        </div>

                                    @endforelse

                                </div>

                                <div class="my-4 flex items-center justify-between rounded-lg bg-gray-50 px-4 py-4">
                                    <span class="font-semibold text-gray-700">
                                        Total contado
                                    </span>

                                    <span class="text-lg font-bold tabular-nums text-gray-900">
                                        {{ number_format(
                                            (float) $this->montoTotalContado(),
                                            2,
                                            '.',
                                            ','
                                        ) }}
                                        {{ $monedaCodigo }}
                                    </span>
                                </div>


                            </section>

                        </div>

                    </div>


                    {{-- ====================================================
                        FOOTER
                        ==================================================== --}}
                    <div
                        class="flex shrink-0 items-center justify-end gap-3 border-t border-gray-200 bg-white px-6 py-4"
                    >

                        <button
                            type="button"
                            wire:click="cerrar"
                            class="btn-secondary"
                        >
                            Cerrar
                        </button>

                        <button
                            type="button"
                            wire:click="guardar"
                            @disabled(!$this->hayCambios())
                            @class([
                                'btn-primary',
                                'opacity-50 cursor-not-allowed' => !$this->hayCambios(),
                            ])
                        >
                            Guardar
                        </button>

                    </div>

                </div>

            </div>

        @endif

    </div>
