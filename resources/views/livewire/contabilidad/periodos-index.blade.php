<div>
    <div class="w-full border border-slate-200 rounded-2xl px-1 py-2 bg-white shadow-sm">
        <div class="flex items-center justify-center mb-3">
            <h1 class="text-xl font-bold text-slate-800 md:text-2xl">
                Resumen de Periodos Contables
            </h1>
        </div>
        @foreach($periodosContables as $periodo)
            @php
            // Soporte a nombres de columnas: fecha_inicio/fin o fecha_inicial/final
            $fi = $periodo->fecha_inicio ?? $periodo->fecha_inicial ?? null;
            $ff = $periodo->fecha_fin    ?? $periodo->fecha_fin     ?? null;
            
            @endphp
            <div class="mt-3 shadow-2xl">
                Rango: <strong>{{ $fi ? format_fecha($fi) : '—' }}</strong>
                –   <strong>{{ $ff ? format_fecha($ff) : '—' }}</strong>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th>Ventas</th>
                            <th>Gastos</th>
                            <th>Inventario</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        <tr>
                            <td>
                                <div class="px-2 text-gray-400 flex items-center justify-center">
                                    0
                                </div>
                            </td>
                            <td>
                                <div class="px-2 text-gray-400 flex items-center justify-center">
                                    0
                                </div>
                            </td>
                            <td>
                                <div class="px-2 text-gray-400 flex items-center justify-center">
                                    0
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endforeach
    </div>
</div>
