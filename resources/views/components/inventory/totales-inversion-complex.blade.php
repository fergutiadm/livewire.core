@php
  // Formateadores
  $fmt    = fn($n) => number_format((float)$n, 2, '.', ','); // bonito
  //$fmtRaw = fn($n) => (string) (int) round((float)$n);       // crudo
  $fmtRaw = fn($n) => number_format((float)$n, 2, '.', ''); // en vez de (string)(int) round(...)
  $print  = !empty($raw) ? $fmtRaw : $fmt;

  // Helper para pintar una línea "En stock / En cero / Todos"
  $row = function(string $label, $value) use ($print) {
      return <<<HTML
        <div class="flex items-center justify-between text-sm">
          <span class="text-slate-500">{$label}</span>
          <span class="font-semibold text-slate-900">{$print($value)}</span>
        </div>
      HTML;
  };
@endphp

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
  @if(!empty($title))
    <div class="text-center text-sm font-extrabold text-slate-800 tracking-wide uppercase mb-4">
      {{ $title }}
    </div>
  @endif

  <div class="grid grid-cols-12 gap-4 items-stretch">
    @foreach($cards as $card)
      <div class="col-span-12 md:col-span-4">
        <div class="rounded-xl border border-slate-200 p-3 bg-slate-50/40 h-full flex flex-col gap-3">
          <div class="text-[11px] uppercase text-slate-500">{{ $card['label'] }}</div>

          {{-- Bloque USD (si existe) --}}
          @if(!is_null($card['usd'] ?? null))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50/50 p-2">
              <div class="mb-1 flex items-center gap-2">
                <small class="px-1.5 py-0.5 rounded-md text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">USD</small>
                <span class="text-[11px] uppercase text-emerald-700">Valores</span>
              </div>
              <div class="space-y-1.5">
                {!! $row('En stock', $card['usd']['stock'] ?? 0) !!}
                {!! $row('En cero',   $card['usd']['zero']  ?? 0) !!}
                <div class="pt-1 border-t border-emerald-100">
                  {!! $row('Todos', $card['usd']['all'] ?? 0) !!}
                </div>
              </div>
            </div>
          @endif

          {{-- Bloque PRIMARIA --}}
          <div class="rounded-lg border border-sky-200 bg-sky-50/50 p-2">
            <div class="mb-1 flex items-center gap-2">
              <small class="px-1.5 py-0.5 rounded-md text-[10px] font-semibold bg-sky-50 text-sky-700 border border-sky-200">{{ $primaryCode ?? 'PRIM' }}</small>
              <span class="text-[11px] uppercase text-sky-700">Valores</span>
            </div>
            <div class="space-y-1.5">
              {!! $row('En stock', $card['primary']['stock'] ?? 0) !!}
              {!! $row('En cero',   $card['primary']['zero']  ?? 0) !!}
              <div class="pt-1 border-t border-sky-100">
                {!! $row('Todos', $card['primary']['all'] ?? 0) !!}
              </div>
            </div>
          </div>

        </div>
      </div>
    @endforeach
  </div>
</div>
