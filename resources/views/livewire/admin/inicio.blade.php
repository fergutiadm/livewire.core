<div>
    {{-- Aviso de periodo contable activo --}}
        @php
        // Soporte a nombres de columnas: fecha_inicio/fin o fecha_inicial/final
        $fi = $periodoActivo->fecha_inicio ?? $periodoActivo->fecha_inicial ?? null;
        $ff = $periodoActivo->fecha_fin    ?? $periodoActivo->fecha_fin    ?? null;

        $fmt = function($d) {
            try { return \Carbon\Carbon::parse($d)->isoFormat('DD/MM/YYYY'); } catch(\Throwable $e) { return $d; }
        };
        @endphp

        @if($periodoActivo)
        <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 p-3 sm:p-4">
            <div class="flex flex-wrap items-center gap-2 text-sm">
            <span class="inline-flex items-center rounded-full bg-emerald-600 px-2 py-0.5 text-white text-xs font-semibold">Periodo activo</span>
            @if($fi || $ff)
                <span class="text-emerald-900">
                Rango: <strong>{{ $fi ? $fmt($fi) : '—' }}</strong>
                – <strong>{{ $ff ? $fmt($ff) : '—' }}</strong>
                </span>
            @endif
            @if(optional($periodoActivo->local)->nombre)
                <span class="text-emerald-900">· Local:
                <strong>{{ $periodoActivo->local->nombre }}</strong>
                </span>
            @endif
            @if($periodoActivo->descripcion)
                <span class="text-emerald-900">· {{ $periodoActivo->descripcion }}</span>
            @endif

            <a href="{{ route('admin.periodos_contables') }}"
                class="ml-auto inline-flex items-center rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700">
                Gestionar periodos
            </a>
            </div>
        </div>
        @else
        <div class="mb-4 rounded-xl border border-amber-200 bg-amber-50 p-3 sm:p-4">
            <div class="flex items-center gap-2 text-sm text-amber-900">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 1 1 0 20A10 10 0 0 1 12 2Zm1 5h-2v7h2V7Zm0 8h-2v2h2v-2Z"/></svg>
            No hay periodo contable activo.
            <a href="{{ route('admin.periodos_contables') }}"
                class="ml-2 inline-flex items-center rounded-lg bg-amber-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-amber-700">
                Crear periodo
            </a>
            </div>
        </div>
        @endif

        {{-- Tarjetas de métricas --}}
      <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
          <div class="text-sm text-gray-500">Productos</div>
          <div class="mt-1 text-2xl font-semibold text-gray-900">{{ $stats['productos'] ?? 0 }}</div>
          <div class="mt-2 text-xs text-gray-400">Total en catálogo</div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
          <div class="text-sm text-gray-500">Categorías</div>
          <div class="mt-1 text-2xl font-semibold text-gray-900">{{ $stats['categorias'] ?? 0 }}</div>
          <div class="mt-2 text-xs text-gray-400">Registradas</div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
            <div class="text-sm text-gray-500">Atributos</div>
            <div class="mt-1 text-2xl font-semibold text-gray-900">{{ number_format($stats['atributos'] ?? 0) }}</div>
            <div class="mt-2 text-xs text-gray-400">Registrados</div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
            <div class="text-sm text-gray-500">Gestores</div>
            <div class="mt-1 text-2xl font-semibold text-gray-900">{{ $stats['gestores'] ?? 0 }}</div>
            <div class="mt-2 text-xs text-gray-400">Con acceso</div>
        </div>
      </div>

      {{-- (Opcional) tarjeta de ventas si existe orders --}}
      @if(($ventasTotales ?? 0) > 0)
        <div class="mt-4 grid grid-cols-1">
          <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
            <div class="text-sm text-gray-500">Ventas acumuladas</div>
            <div class="mt-1 text-2xl font-semibold text-gray-900">
              ${{ number_format($ventasTotales, 2) }}
            </div>
          </div>
        </div>
      @endif

      {{-- Últimos productos --}}
    <div class="mt-6 bg-white rounded-xl shadow ring-1 ring-gray-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
        <div class="text-sm font-semibold text-gray-900">Últimos productos</div>
        <a class="text-sm font-medium text-blue-600 hover:text-blue-700"
            href="{{ route('admin.productos') }}">Ver todos</a>
        </div>

        <div class="w-full overflow-x-auto">
        <table class="w-full min-w-full table-auto align-middle">
            <thead>
            <tr>
                <th style="width:120px">Imagen</th>
                <th>Nombre</th>
                <th class="hidden sm:table-cell">Código</th>
                <th class="hidden sm:table-cell">Categoría</th>
                <th class="text-end">Costo</th>
                <th class="text-end">Precio</th>
                <th class="text-end" style="width:160px"></th>
            </tr>
            </thead>

            <tbody>
            @forelse(($latestProducts ?? []) as $p)
                @php
                // === mismo bloque de media que en el index ===
                $toUrl = function($path) {
                    $raw = trim($path ?? '');
                    if ($raw === '') return null;
                    if (preg_match('~^https?://~i',$raw)) return $raw;
                    if (str_starts_with($raw,'public/storage/')) return asset(substr($raw,7));
                    if (str_starts_with($raw,'storage/')) return asset($raw);
                    if (\Storage::disk('public')->exists($raw)) return \Storage::url($raw);
                    if (file_exists(public_path($raw))) return asset($raw);
                    return null;
                };

                $media    = collect($p->media ?? []);
                $primary  = $media->firstWhere('is_primary', true) ?? $media->first();
                $others   = $media->filter(fn($m)=> $primary ? $m->id !== $primary->id : true);
                $ordered  = collect($primary ? [$primary] : [])->merge($others);

                $imgUrls  = $ordered->map(fn($m)=> $toUrl($m->path))->filter()->values();

                $placeholder = asset('img/placeholder_zapato_detalle.svg');
                $mainUrl     = $imgUrls->first() ?: $placeholder;

                $hasRealGallery = $imgUrls->count() > 1
                    || ($imgUrls->count() === 1 && !\Illuminate\Support\Str::contains($imgUrls->first(), 'placeholder_zapato_detalle.svg'));
                @endphp

                <tr class="hover:bg-gray-50">
                <td class="nlx-td-img shrink-0">
                    @if($hasRealGallery)
                    <a href="{ route('zapatera.admin.products.gallery', ['product'=>$p->id, 'i'=>0]) }"
                        class="d-inline-block" aria-label="Ver galería del producto">
                        <img src="{{ $mainUrl }}" class="nlx-thumb-main img-thumbnail" alt="{{ $p->name }}" loading="lazy">
                    </a>
                    @else
                    <img src="{{ $mainUrl }}" class="nlx-thumb-main img-thumbnail" alt="{{ $p->name }}" loading="lazy">
                    @endif
                </td>

                {{-- Nombre + SKU (badge debajo, oculto en sm-) --}}
                <td class="fw-semibold align-middle min-w-0">
                    <div class="text-gray-900">{{ $p->name }}</div>
                    @if($p->sku)
                    <div class="mt-1 hidden sm:block">
                        <span class="inline-flex items-center rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200 px-2 py-0.5 text-[11px] font-medium">
                        {{ $p->nombre }}
                        </span>
                    </div>
                    @endif
                </td>

                <td class="hidden sm:table-cell">
                    @if($p->barcode)
                    <span class="badge text-bg-light">{{ $p->codigo }}</span>
                    @endif
                </td>

                <td class="hidden sm:table-cell">{{ optional($p->categoria)->nombre }}</td>

                {{-- Costo --}}
                <td class="text-end tabular-nums shrink-0">
                    <span class="sm:hidden">${{ (int) ($p->costo ?? 0) }}</span>
                    <span class="hidden sm:inline">${{ number_format((float) ($p->costo ?? 0), 2) }}</span>
                </td>

                {{-- Precio --}}
                <td class="text-end tabular-nums shrink-0">
                    <span class="sm:hidden">${{ (int) ($p->precio ?? 0) }}</span>
                    <span class="hidden sm:inline">${{ number_format((float) ($p->precio ?? 0), 2) }}</span>
                </td>

                <td class="text-right whitespace-nowrap shrink-0">
                    <a href="{{ route('zapatera.admin.products.edit',$p) }}"
                    class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold
                            bg-white border border-gray-300 text-gray-700 hover:bg-gray-50">
                    Editar
                    </a>
                </td>
                </tr>
            @empty
                <tr>
                <td colspan="7" class="text-center text-muted py-4">No hay productos recientes.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
        </div>
    </div>
</div>
