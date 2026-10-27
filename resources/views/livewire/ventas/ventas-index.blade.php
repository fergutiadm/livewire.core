{{-- resources/views/livewire/ventas/ventas-index.blade.php --}}

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
    @php
        /*
         * DATOS FICTICIOS PARA DISEÑO.
         *
         * Los importes de líneas, ventas y desgloses ya están preparados.
         * No se realizan cálculos financieros en Blade.
         *
         * importe: importe total de la línea para su cantidad.
         * desgloses[].importe: importe total del desglose para esa cantidad.
         */

        $gruposVentasDemo = [
            [
                'fecha' => '24/10/2026',
                'fecha_iso' => '2026-10-24',
                'etiqueta' => 'Hoy',
                'ventas' => [
                    [
                        'id' => 1001,
                        'vale' => 'V-01001',
                        'cliente' => 'Cliente mostrador',
                        'tipo' => 'Producto',
                        'descripcion' => 'Venta de productos',
                        'pendiente' => false,
                        'editada' => false,
                        'total' => '12.500,00',
                        'moneda' => 'CUP',
                        'lineas' => [
                            [
                                'nombre' => 'Lavadora XC1234',
                                'cantidad' => '1',
                                'precio' => '10.000,00',
                                'importe' => '10.000,00',
                                'moneda' => 'CUP',
                                'atributos' => ['Marca: LG', 'Capacidad: 15 kg'],
                                'desgloses' => [],
                            ],
                            [
                                'nombre' => 'Accesorio de instalación',
                                'cantidad' => '1',
                                'precio' => '2.500,00',
                                'importe' => '2.500,00',
                                'moneda' => 'CUP',
                                'atributos' => [],
                                'desgloses' => [],
                            ],
                        ],
                    ],
                    [
                        'id' => 1002,
                        'vale' => 'V-01002',
                        'cliente' => 'Cliente ocasional',
                        'tipo' => 'Servicio',
                        'descripcion' => 'Servicio técnico',
                        'pendiente' => true,
                        'editada' => false,
                        'total' => '3.500,00',
                        'moneda' => 'CUP',
                        'lineas' => [
                            [
                                'nombre' => 'Servicio técnico',
                                'cantidad' => '1',
                                'precio' => '3.500,00',
                                'importe' => '3.500,00',
                                'moneda' => 'CUP',
                                'atributos' => [],
                                'desgloses' => [],
                            ],
                        ],
                    ],
                    [
                        'id' => 1003,
                        'vale' => 'V-01003',
                        'cliente' => 'Liz Valery Azcuy Ruiz',
                        'tipo' => 'Producto',
                        'descripcion' => 'Venta de productos',
                        'pendiente' => false,
                        'editada' => false,
                        'total' => '9.250,00',
                        'moneda' => 'CUP',
                        'lineas' => [
                            [
                                'nombre' => 'Memoria USB',
                                'cantidad' => '2',
                                'precio' => '4.500,00',
                                'importe' => '9.000,00',
                                'moneda' => 'CUP',
                                'atributos' => ['Marca: ADATA', 'Capacidad: 64 GB'],
                                'desgloses' => [],
                            ],
                            [
                                'nombre' => 'Teléfono',
                                'cantidad' => '1',
                                'precio' => '250,00',
                                'importe' => '250,00',
                                'moneda' => 'CUP',
                                'atributos' => [
                                    'Marca: Samsung Galaxy A16',
                                    'Modelo: SM-A165FZADMEA',
                                    'Color: Gris/Gray',
                                    'Capacidad: 128 GB',
                                ],
                                'desgloses' => [],
                            ],
                        ],
                    ],
                    [
                        'id' => 1004,
                        'vale' => 'V-01004',
                        'cliente' => 'Yunger Azcuy Torrens',
                        'tipo' => 'Servicio',
                        'descripcion' => 'Servicio técnico',
                        'pendiente' => true,
                        'editada' => false,
                        'total' => '2.500,00',
                        'moneda' => 'CUP',
                        'lineas' => [
                            [
                                'nombre' => 'Pegar pantalla Huawei P2019',
                                'cantidad' => '1',
                                'precio' => '2.500,00',
                                'importe' => '2.500,00',
                                'moneda' => 'CUP',
                                'atributos' => [],
                                'desgloses' => [],
                            ],
                        ],
                    ],
                    [
                        'id' => 1005,
                        'vale' => 'V-01005',
                        'cliente' => 'Cliente multimoneda',
                        'tipo' => 'Producto',
                        'descripcion' => 'Ejemplo de pago en dos monedas',
                        'pendiente' => false,
                        'editada' => false,
                        'total' => '15.000,00',
                        'moneda' => 'CUP',
                        'lineas' => [
                            [
                                'nombre' => 'Disco duro Externo',
                                'cantidad' => '1',
                                'precio' => '15.000,00',
                                'importe' => '15.000,00',
                                'moneda' => 'CUP',
                                'atributos' => [
                                    'Marca: Toshiba',
                                    'Modelo: TH-43GFGDS03',
                                    'Color: Negro',
                                    'Capacidad: 2 TB',
                                ],
                                'desgloses' => [
                                    [
                                        'moneda' => 'USD',
                                        'importe' => '7,00',
                                    ],
                                    [
                                        'moneda' => 'CUP',
                                        'importe' => '9.050,00',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'fecha' => '23/10/2026',
                'fecha_iso' => '2026-10-23',
                'etiqueta' => 'Día anterior',
                'ventas' => [
                    [
                        'id' => 2001,
                        'vale' => 'V-02001',
                        'cliente' => 'Cliente habitual',
                        'tipo' => 'Producto',
                        'descripcion' => 'Venta al por mayor',
                        'pendiente' => false,
                        'editada' => true,
                        'total' => '28.750,00',
                        'moneda' => 'CUP',
                        'lineas' => [
                            [
                                'nombre' => 'Producto de ejemplo A',
                                'cantidad' => '5',
                                'precio' => '5.750,00',
                                'importe' => '28.750,00',
                                'moneda' => 'CUP',
                                'atributos' => ['Presentación: Unidad'],
                                'desgloses' => [],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'fecha' => '22/10/2026',
                'fecha_iso' => '2026-10-22',
                'etiqueta' => 'Hace dos días',
                'ventas' => [
                    [
                        'id' => 3001,
                        'vale' => 'V-03001',
                        'cliente' => 'Cliente mostrador',
                        'tipo' => 'Producto',
                        'descripcion' => 'Venta de productos',
                        'pendiente' => false,
                        'editada' => false,
                        'total' => '8.400,00',
                        'moneda' => 'CUP',
                        'lineas' => [
                            [
                                'nombre' => 'Producto de ejemplo B',
                                'cantidad' => '2',
                                'precio' => '4.200,00',
                                'importe' => '8.400,00',
                                'moneda' => 'CUP',
                                'atributos' => [],
                                'desgloses' => [],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $gruposVentasVisibles = array_values(array_filter(
            $gruposVentasDemo,
            fn ($grupo) => $grupo['fecha_iso'] === $fechaOperacion
        ));

        $idsVentasVisibles = [];

        foreach ($gruposVentasVisibles as $grupo) {
            foreach ($grupo['ventas'] as $ventaDemo) {
                $idsVentasVisibles[] = $ventaDemo['id'];
            }
        }
    @endphp

    <x-ui.crud.page class="pt-6" layout="horizontal">
        <x-slot name="header">
            <x-ui.crud.header
                title="Ventas"
                description="Consulta y organiza las ventas por fecha"
                :show-form="$showForm"
                :show-toggle="false"
                :selector-periodo="true"
                :selector-fecha-operacion="true"
                :gestion-cierre="true"
            />
            <div class="flex justify-end">
                <button
                    type="button"
                    @click="mostrarResumenes = !mostrarResumenes"
                    class="text-sm font-medium text-indigo-600 hover:text-indigo-800"
                    :aria-expanded="mostrarResumenes.toString()"
                >
                    <span
                        x-text="mostrarResumenes
                            ? 'Ocultar resúmenes'
                            : 'Ver resúmenes'"
                    ></span>
                </button>
            </div>
            {{-- Resúmenes de demostración --}}
            <section
                x-cloak
                x-show="mostrarResumenes"
                x-transition
                class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-5"
            >

                {{-- Resumen del período --}}
                <article class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                    <p class="flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <span class="h-2 w-2 shrink-0 rounded-full bg-emerald-500"></span>
                        Resumen del período
                    </p>

                    <p class="mt-3 text-xs text-slate-500">Ventas</p>
                    <p class="text-xl font-bold tabular-nums text-slate-900">
                        125.000,00 <span class="text-xs font-semibold text-slate-500">CUP</span>
                    </p>

                    <div class="mt-3 grid grid-cols-2 gap-3 border-t border-slate-100 pt-3">
                        <div>
                            <p class="text-xs text-slate-500">Gastos</p>
                            <p class="mt-1 text-sm font-semibold tabular-nums text-slate-700">
                                32.750,00
                            </p>
                            <span class="text-[10px] text-slate-400">CUP</span>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500">Ganancias</p>
                            <p class="mt-1 text-sm font-semibold tabular-nums text-slate-700">
                                42.000,00
                            </p>
                            <span class="text-[10px] text-slate-400">CUP · demo</span>
                        </div>
                    </div>
                </article>

                {{-- Resumen del día --}}
                <article class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                    <p class="flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <span class="h-2 w-2 shrink-0 rounded-full bg-emerald-800"></span>
                        Resumen del día
                    </p>

                    <p class="mt-3 text-xs text-slate-500">Ventas</p>
                    <p class="text-xl font-bold tabular-nums text-slate-900">
                        18.500,00 <span class="text-xs font-semibold text-slate-500">CUP</span>
                    </p>

                    <div class="mt-3 grid grid-cols-2 gap-3 border-t border-slate-100 pt-3">
                        <div>
                            <p class="text-xs text-slate-500">Gastos</p>
                            <p class="mt-1 text-sm font-semibold tabular-nums text-slate-700">
                                4.200,00
                            </p>
                            <span class="text-[10px] text-slate-400">CUP</span>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500">Ganancias</p>
                            <p class="mt-1 text-sm font-semibold tabular-nums text-slate-700">
                                6.700,00
                            </p>
                            <span class="text-[10px] text-slate-400">CUP · demo</span>
                        </div>
                    </div>
                </article>

                {{-- Resumen por moneda --}}
                <article class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                    <p class="flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <span class="h-2 w-2 shrink-0 rounded-full bg-indigo-500"></span>
                        Resumen por moneda
                    </p>

                    <div class="mt-4 space-y-3">
                        <div class="flex items-center justify-between gap-2">
                            <span class="rounded-md bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-600">
                                CUP
                            </span>
                            <span class="text-sm font-bold tabular-nums text-slate-900">
                                125.000,00
                            </span>
                        </div>

                        <div class="flex items-center justify-between gap-2">
                            <span class="rounded-md bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-600">
                                USD
                            </span>
                            <span class="text-sm font-bold tabular-nums text-slate-900">
                                150,00
                            </span>
                        </div>
                    </div>

                    <p class="mt-4 border-t border-slate-100 pt-3 text-[10px] text-slate-400">
                        Importes separados; no se suman monedas distintas.
                    </p>
                </article>

                {{-- Resumen del fondo --}}
                <article class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                    <p class="flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <span class="h-2 w-2 shrink-0 rounded-full bg-purple-500"></span>
                        Resumen del fondo
                    </p>

                    <div class="mt-4 space-y-3">
                        <div class="flex items-center justify-between gap-2">
                            <span class="rounded-md bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-600">
                                CUP
                            </span>
                            <span class="text-sm font-bold tabular-nums text-slate-900">
                                250.000,00
                            </span>
                        </div>

                        <div class="flex items-center justify-between gap-2">
                            <span class="rounded-md bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-600">
                                USD
                            </span>
                            <span class="text-sm font-bold tabular-nums text-slate-900">
                                450,00
                            </span>
                        </div>
                    </div>

                    <p class="mt-4 border-t border-slate-100 pt-3 text-[10px] text-slate-400">
                        Importes separados; no se suman monedas distintas.
                    </p>
                </article>

                {{-- Resumen de salarios --}}
                <article class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                    <p class="flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <span class="h-2 w-2 shrink-0 rounded-full bg-fuchsia-500"></span>
                        Resumen de salarios
                    </p>

                    <p class="mt-3 text-xs text-slate-500">Importe ilustrativo</p>
                    <p class="text-xl font-bold tabular-nums text-slate-900">
                        24.500,00
                    </p>
                    <span class="text-xs font-semibold text-slate-500">CUP</span>

                    <div class="mt-4 border-t border-slate-100 pt-3">
                        <livewire:resumenes.salario-trabajadores />
                        {{-- <p class="text-xs text-slate-500 cursor-pointer font-semibold">Trabajadores...</p>
                        <p class="mt-1 text-sm font-semibold text-slate-700">5 · demo</p> --}}
                    </div>
                </article>

            </section>
        </x-slot>

        <x-slot name="head_container">
            <div class="grid w-full grid-cols-1 gap-6 md:grid-cols-[minmax(0,3fr)_minmax(280px,1fr)]">
        </x-slot>

        <x-slot name="form">
            <div class="min-w-0 md:order-2">
                <x-ui.crud.form :show="$showForm">
                    <div class="space-y-4">
                        <div>
                            <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                                Próximamente
                            </span>
                            <h2 class="mt-1 text-lg font-semibold text-slate-800">
                                Formulario de venta
                            </h2>
                            <p class="mt-1 text-sm leading-6 text-slate-500">
                                En esta sección se incorporará el registro y la edición de ventas.
                            </p>
                        </div>

                        <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-5">
                            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-white text-slate-400 shadow-sm">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                    <path d="M3 3h18v18H3z"/>
                                    <path d="M8 8h8M8 12h8M8 16h5"/>
                                </svg>
                            </div>
                            <p class="mt-3 text-sm font-medium text-slate-700">Espacio reservado</p>
                            <p class="mt-1 text-xs leading-5 text-slate-500">
                                El formulario se implementará cuando definamos el flujo de ventas.
                            </p>
                        </div>
                    </div>
                </x-ui.crud.form>
            </div>
        </x-slot>

        <x-slot:table>
            <div class="min-w-0 md:order-1">
                <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-bold tracking-tight text-slate-900">
                            Registro de ventas
                        </h2>
                        <p class="mt-1 text-sm text-slate-500">
                            @if ($fechaOperacion)
                                Ventas del {{ format_fecha($fechaOperacion) }}
                            @else
                                Selecciona una fecha para consultar las ventas.
                            @endif
                        </p>
                    </div>

                    @if (count($idsVentasVisibles) > 0)
                        <button
                            type="button"
                            wire:click="toggleTodosDetallesVenta(@js($idsVentasVisibles))"
                            aria-pressed="{{ $verDetallesVenta ? 'true' : 'false' }}"
                            class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/30"
                        >
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path d="M3 12s3-7 9-7 9 7 9 7-3 7-9 7-9-7-9-7Z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                            {{ $verDetallesVenta ? 'Ocultar todos los detalles' : 'Ver todos los detalles' }}
                        </button>
                    @endif
                </div>

                <div class="mb-5 flex items-start gap-3 rounded-xl border border-amber-200 bg-amber-50/70 p-3.5">
                    <svg class="mt-0.5 h-5 w-5 shrink-0 text-amber-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <circle cx="12" cy="12" r="9"/>
                        <path d="M12 11v5M12 7h.01"/>
                    </svg>
                    <div>
                        <p class="text-xs font-semibold text-amber-900">Vista de demostración</p>
                        <p class="mt-0.5 text-xs leading-5 text-amber-800">
                            Las ventas, cantidades e importes son ficticios. Los importes se han preparado para diseñar la interfaz, no se calculan ni consultan datos reales.
                        </p>
                    </div>
                </div>

                @if (empty($gruposVentasVisibles))
                    <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-5 py-12 text-center">
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <rect x="3" y="4" width="18" height="17" rx="2"/>
                                <path d="M16 2v4M8 2v4M3 9h18"/>
                            </svg>
                        </div>
                        <h3 class="mt-4 text-sm font-semibold text-slate-800">No hay ventas para esta fecha</h3>
                        <p class="mt-1 text-xs leading-5 text-slate-500">
                            @if ($fechaOperacion)
                                No hay ventas de demostración para {{ format_fecha($fechaOperacion) }}.
                            @else
                                Selecciona una fecha para consultar las ventas.
                            @endif
                        </p>
                        <p class="mt-2 text-xs text-slate-400">
                            Esto no representa una consulta a ventas reales.
                        </p>
                    </div>
                @else
                    <div class="space-y-8">
                        @foreach ($gruposVentasVisibles as $grupo)
                            <section wire:key="grupo-ventas-demo-{{ $grupo['fecha_iso'] }}" class="space-y-3">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-indigo-700">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                            <rect x="3" y="5" width="18" height="16" rx="2"/>
                                            <path d="M16 3v4M8 3v4M3 10h18"/>
                                        </svg>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <h3 class="text-sm font-bold text-slate-800">{{ $grupo['fecha'] }}</h3>
                                            <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-medium text-slate-600">
                                                {{ $grupo['etiqueta'] }}
                                            </span>
                                        </div>
                                        <p class="mt-0.5 text-xs text-slate-500">
                                            {{ count($grupo['ventas']) }}
                                            {{ count($grupo['ventas']) === 1 ? 'venta' : 'ventas' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 gap-4 lg:grid-cols-2 2xl:grid-cols-3">
                                    @foreach ($grupo['ventas'] as $venta)
                                        @php
                                            $detallesVisibles = in_array(
                                                $venta['id'],
                                                $detallesVentaVisibles
                                            );
                                        @endphp

                                        <article
                                            wire:key="venta-demo-{{ $venta['id'] }}"
                                            class="group flex min-w-0 flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-slate-300 hover:shadow-lg"
                                        >
                                            <header class="border-b border-slate-100 bg-gradient-to-br from-slate-50 to-white p-4">
                                                <div class="flex items-start justify-between gap-3">
                                                    <div class="flex min-w-0 items-start gap-3">
                                                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-indigo-100 text-indigo-700">
                                                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                                                <path d="M6 3h9l4 4v14H6z"/>
                                                                <path d="M14 3v5h5M9 12h7M9 16h7"/>
                                                            </svg>
                                                        </div>
                                                        <div class="min-w-0">
                                                            <p class="truncate text-sm font-bold text-slate-900">{{ $venta['vale'] }}</p>
                                                            <p class="mt-1 truncate text-xs text-slate-500">{{ $venta['cliente'] }}</p>
                                                        </div>
                                                    </div>
                                                    <span class="shrink-0 rounded-lg bg-slate-100 px-2 py-1 text-[10px] font-semibold text-slate-600">
                                                        {{ $venta['tipo'] }}
                                                    </span>
                                                </div>

                                                <div class="mt-3 flex flex-wrap gap-1.5">
                                                    @if ($venta['pendiente'])
                                                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2 py-1 text-[10px] font-semibold text-amber-700 ring-1 ring-inset ring-amber-200">
                                                            <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                                            Pendiente
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-1 text-[10px] font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-200">
                                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                                            Registrada
                                                        </span>
                                                    @endif

                                                    @if ($venta['editada'])
                                                        <span class="inline-flex items-center gap-1 rounded-full bg-indigo-50 px-2 py-1 text-[10px] font-semibold text-indigo-700 ring-1 ring-inset ring-indigo-200">
                                                            <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                                                <path d="m16 4 4 4L9 19H5v-4Z"/>
                                                            </svg>
                                                            Editada
                                                        </span>
                                                    @endif
                                                </div>
                                            </header>

                                            <div class="p-4">
                                                <p class="text-xs font-medium text-slate-500">Total de la venta</p>
                                                <div class="mt-1 flex flex-wrap items-baseline gap-2">
                                                    <p class="text-2xl font-bold tracking-tight tabular-nums text-slate-900">
                                                        {{ $venta['total'] }}
                                                    </p>
                                                    <span class="text-xs font-semibold text-slate-500">{{ $venta['moneda'] }}</span>
                                                </div>
                                                <p class="mt-1 truncate text-xs text-slate-500">{{ $venta['descripcion'] }}</p>
                                            </div>

                                            @if ($detallesVisibles)
                                                <div class="border-t border-slate-100 bg-slate-50/70 p-4">
                                                    <div class="mb-3 flex items-center justify-between gap-2">
                                                        <h4 class="text-xs font-bold uppercase tracking-wide text-slate-600">
                                                            Artículos y servicios
                                                        </h4>
                                                        <span class="text-[10px] text-slate-400">
                                                            {{ count($venta['lineas']) }}
                                                            {{ count($venta['lineas']) === 1 ? 'línea' : 'líneas' }}
                                                        </span>
                                                    </div>

                                                    <div class="space-y-4">
                                                        @foreach ($venta['lineas'] as $linea)
                                                            <div wire:key="linea-demo-{{ $venta['id'] }}-{{ $loop->index }}" class="rounded-xl border border-slate-200 bg-white p-3">
                                                                <div class="flex items-start gap-3">
                                                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-slate-200 bg-slate-50 text-slate-400">
                                                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                                                            <path d="m4 7 8-4 8 4-8 4Z"/>
                                                                            <path d="m4 7 8 4 8-4M4 7v10l8 4 8-4V7M12 11v10"/>
                                                                        </svg>
                                                                    </div>

                                                                    <div class="min-w-0 flex-1">
                                                                        <p class="text-xs font-semibold leading-5 text-slate-800">{{ $linea['nombre'] }}</p>
                                                                        <p class="mt-1 text-[11px] text-slate-500">
                                                                            {{ $linea['cantidad'] }} × {{ $linea['precio'] }} {{ $linea['moneda'] }}
                                                                        </p>
                                                                    </div>
                                                                </div>

                                                                <div class="mt-3 flex items-center justify-between gap-3 border-t border-slate-100 pt-3">
                                                                    <span class="text-xs text-slate-500">Importe de la línea</span>
                                                                    <span class="text-sm font-bold tabular-nums text-slate-800">
                                                                        {{ $linea['importe'] }} {{ $linea['moneda'] }}
                                                                    </span>
                                                                </div>

                                                                @if (count($linea['atributos']))
                                                                    <div class="mt-3 flex flex-wrap gap-1">
                                                                        @foreach ($linea['atributos'] as $atributo)
                                                                            <span
                                                                                wire:key="atributo-demo-{{ $venta['id'] }}-{{ $loop->parent->index }}-{{ $loop->index }}"
                                                                                class="rounded-md bg-slate-100 px-1.5 py-1 text-[10px] text-slate-500"
                                                                            >
                                                                                {{ $atributo }}
                                                                            </span>
                                                                        @endforeach
                                                                    </div>
                                                                @endif

                                                                @if (count($linea['desgloses']))
                                                                    <div class="mt-4 rounded-lg border border-indigo-100 bg-indigo-50/60 p-3">
                                                                        <div class="mb-2 flex items-center justify-between gap-2">
                                                                            <p class="text-[11px] font-bold uppercase tracking-wide text-indigo-800">
                                                                                Desglose del pago
                                                                            </p>
                                                                            <span class="text-[10px] text-indigo-600">Por moneda</span>
                                                                        </div>

                                                                        <div class="space-y-2">
                                                                            @foreach ($linea['desgloses'] as $desglose)
                                                                                <div
                                                                                    wire:key="desglose-demo-{{ $venta['id'] }}-{{ $loop->parent->index }}-{{ $loop->index }}"
                                                                                    class="flex items-center justify-between gap-3"
                                                                                >
                                                                                    <span class="text-xs text-slate-600">
                                                                                        {{ $desglose['moneda'] }}
                                                                                    </span>
                                                                                    <span class="text-sm font-semibold tabular-nums text-slate-800">
                                                                                        {{ $desglose['importe'] }} {{ $desglose['moneda'] }}
                                                                                    </span>
                                                                                </div>
                                                                            @endforeach
                                                                        </div>

                                                                        <p class="mt-2 text-[10px] leading-4 text-indigo-700">
                                                                            Importes ilustrativos del desglose para la cantidad indicada. Las monedas se muestran por separado.
                                                                        </p>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif

                                            <footer class="mt-auto flex items-center justify-between gap-3 border-t border-slate-100 px-4 py-3">
                                                <span class="text-[10px] text-slate-400">Datos ficticios</span>

                                                <button
                                                    type="button"
                                                    wire:click="toggleDetalleVenta({{ $venta['id'] }}, @js($idsVentasVisibles))"
                                                    aria-expanded="{{ $detallesVisibles ? 'true' : 'false' }}"
                                                    class="inline-flex items-center gap-1 text-xs font-semibold text-indigo-600 transition hover:text-indigo-800"
                                                >
                                                    {{ $detallesVisibles ? 'Ocultar detalles' : 'Ver detalles' }}
                                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                                        @if ($detallesVisibles)
                                                            <path d="m18 15-6-6-6 6"/>
                                                        @else
                                                            <path d="m6 9 6 6 6-6"/>
                                                        @endif
                                                    </svg>
                                                </button>
                                            </footer>
                                        </article>
                                    @endforeach
                                </div>
                            </section>
                        @endforeach
                    </div>
                @endif
            </div>
        </x-slot:table>

        <x-slot name="foot_container">
            </div>
        </x-slot>

        <x-slot name="deleteModal">
            <x-ui.crud.delete-modal :show="$showDeleteModal" />
        </x-slot>
    </x-ui.crud.page>

    <x-ui.loading-overlay />
</div>