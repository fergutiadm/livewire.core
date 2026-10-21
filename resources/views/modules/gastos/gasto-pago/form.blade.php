<div class="space-y-6">

    @if($gasto)
        <div class="rounded-xl border bg-white p-5 shadow-sm">

            <div class="mb-4">
                <h3 class="text-lg font-semibold">
                    Registrar pago
                </h3>

                <p class="text-sm text-gray-500">
                    Gasto #{{ $gasto->id }}
                </p>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">

                <div>
                    <div class="text-xs text-gray-500">
                        Importe CUP
                    </div>

                    <div class="font-semibold">
                        {{ $gasto->importe_base }}
                    </div>
                </div>

                <div>
                    <div class="text-xs text-gray-500">
                        Pagado CUP
                    </div>

                    <div class="font-semibold">
                        {{ number_format($gasto->calcularPagadoCup(), 2, '.', '') }}
                    </div>
                </div>

                <div>
                    <div class="text-xs text-gray-500">
                        Pendiente CUP
                    </div>

                    <div class="font-semibold">
                        {{ $gasto->saldo_pendiente_cup }}
                    </div>
                </div>

            </div>
        </div>
    @endif


    <div class="rounded-xl border bg-white p-5 shadow-sm">

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

            <div>
                <label class="block text-sm font-medium">
                    Fecha de pago
                </label>

                <input
                    type="datetime-local"
                    wire:model="fechaPago"
                    class="mt-1 block w-full rounded-lg border-gray-300"
                >
            </div>

            <div>
                <label class="block text-sm font-medium">
                    Descripción
                </label>

                <input
                    type="text"
                    wire:model="descripcion"
                    maxlength="255"
                    class="mt-1 block w-full rounded-lg border-gray-300"
                >
            </div>

        </div>

    </div>


    <div class="rounded-xl border bg-white p-5 shadow-sm">

        <div class="mb-4 flex items-center justify-between">

            <h3 class="text-lg font-semibold">
                Desglose del pago
            </h3>

            <button
                type="button"
                wire:click="agregarDesglose"
                class="rounded-lg border px-3 py-2 text-sm"
            >
                + Agregar
            </button>

        </div>


        <div class="space-y-4">

            @foreach($desgloses as $index => $desglose)

                <div
                    wire:key="gasto-pago-desglose-{{ $index }}"
                    class="rounded-lg border p-4"
                >

                    <div class="grid grid-cols-1 gap-3 md:grid-cols-6">

                        {{-- Moneda --}}
                        <div>
                            <label class="text-xs">
                                Moneda
                            </label>

                            <select
                                wire:model="desgloses.{{ $index }}.moneda_id"
                                class="mt-1 w-full rounded-lg border-gray-300"
                            >
                                <option value="">
                                    Seleccione
                                </option>

                                @foreach($monedas as $moneda)
                                    <option value="{{ $moneda->id }}">
                                        {{ $moneda->codigo }}
                                    </option>
                                @endforeach
                            </select>
                        </div>


                        {{-- Cantidad --}}
                        <div>
                            <label class="text-xs">
                                Cantidad
                            </label>

                            <input
                                type="number"
                                step="0.0000000001"
                                wire:model="desgloses.{{ $index }}.cantidad"
                                class="mt-1 w-full rounded-lg border-gray-300"
                            >
                        </div>


                        {{-- Tasa --}}
                        <div>
                            <label class="text-xs">
                                Tasa
                            </label>

                            <input
                                type="number"
                                step="0.0000000001"
                                wire:model="desgloses.{{ $index }}.tasa_cambio"
                                class="mt-1 w-full rounded-lg border-gray-300"
                            >
                        </div>


                        {{-- Método --}}
                        <div>
                            <label class="text-xs">
                                Método
                            </label>

                            <select
                                wire:model="desgloses.{{ $index }}.metodo"
                                wire:change="metodoChanged({{ $index }})"
                                class="mt-1 w-full rounded-lg border-gray-300"
                            >
                                <option value="efectivo">
                                    Efectivo
                                </option>

                                <option value="tarjeta">
                                    Tarjeta
                                </option>
                            </select>
                        </div>


                        {{-- Tarjeta --}}
                        <div>
                            <label class="text-xs">
                                Tarjeta
                            </label>

                            <select
                                wire:model="desgloses.{{ $index }}.tarjeta_magnetica_id"
                                wire:change="tarjetaChanged({{ $index }})"
                                @disabled(($desglose['metodo'] ?? null) !== 'tarjeta')
                                class="mt-1 w-full rounded-lg border-gray-300"
                            >
                                <option value="">
                                    Seleccione
                                </option>

                                @foreach($tarjetas as $tarjeta)
                                    <option value="{{ $tarjeta->id }}">
                                        {{ $tarjeta->numero }}
                                        ({{ $tarjeta->moneda?->codigo }})
                                    </option>
                                @endforeach
                            </select>
                        </div>


                        {{-- Importe --}}
                        <div>
                            <label class="text-xs">
                                Importe CUP
                            </label>

                            <div class="mt-1 rounded-lg border bg-gray-50 px-3 py-2">
                                {{
                                    number_format(
                                        (float)($desglose['cantidad'] ?? 0)
                                        * (float)($desglose['tasa_cambio'] ?? 0),
                                        2,
                                        '.',
                                        ''
                                    )
                                }}
                            </div>
                        </div>

                    </div>


                    {{-- Snapshot tarjeta --}}
                    @if(($desglose['metodo'] ?? null) === 'tarjeta')

                        <div class="mt-3">

                            <label class="text-xs">
                                Número de tarjeta histórico
                            </label>

                            <input
                                type="text"
                                wire:model="desgloses.{{ $index }}.numero_tarjeta"
                                maxlength="20"
                                readonly
                                class="mt-1 w-full rounded-lg border-gray-300 bg-gray-50"
                            >

                        </div>

                    @endif


                    @if(count($desgloses) > 1)

                        <div class="mt-3 text-right">

                            <button
                                type="button"
                                wire:click="eliminarDesglose({{ $index }})"
                                class="text-sm text-red-600"
                            >
                                Eliminar
                            </button>

                        </div>

                    @endif

                </div>

            @endforeach

        </div>


        <div class="mt-6 border-t pt-4 text-right">

            <div class="text-sm text-gray-500">
                Total del pago
            </div>

            <div class="text-2xl font-bold">
                {{ number_format($this->calcularTotalCup(), 2, '.', '') }}
                CUP
            </div>

        </div>

    </div>


    <div class="flex justify-end gap-3">

        <button
            type="button"
            wire:click="saveDraft"
            class="rounded-lg border px-4 py-2"
        >
            Guardar borrador
        </button>

        @if($pagoId)
            <button
                type="button"
                wire:click="confirmar"
                class="rounded-lg bg-black px-4 py-2 text-white"
            >
                Confirmar pago
            </button>
        @endif

    </div>

</div>