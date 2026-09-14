@php
  $fmt    = fn($n) => number_format((float)$n, 2, '.', ',');
  //$fmtRaw = fn($n) => (string) (int) round((float)$n);
  $fmtRaw = fn($n) => number_format((float)$n, 2, '.', ''); // en vez de (string)(int) round(...)
  $print  = !empty($raw) ? $fmtRaw : $fmt;
@endphp

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
  @if(!empty($title))
    <div class="text-center text-sm font-extrabold text-slate-800 tracking-wide uppercase mb-4">
      {{ $title }}
    </div>
  @endif

  <div class="grid grid-cols-12 gap-4">
    @foreach($cards as $card)
      <div class="col-span-12 md:col-span-4">
        <div class="rounded-xl border border-slate-200 p-3 bg-slate-50/40 h-full flex flex-col gap-3">
          <div class="text-[11px] uppercase text-slate-500">{{ $card['label'] }}</div>

          @if(!is_null($card['usd']))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50/50 p-2">
              <div class="mb-1 flex items-center gap-2">
                <small class="px-1.5 py-0.5 rounded-md text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">USD</small>
                <span class="text-[11px] uppercase text-emerald-700">En stock</span>
              </div>
              <div class="text-right font-semibold">{{ $print($card['usd']) }}</div>
            </div>
          @endif

          <div class="rounded-lg border border-sky-200 bg-sky-50/50 p-2">
            <div class="mb-1 flex items-center gap-2">
              <small class="px-1.5 py-0.5 rounded-md text-[10px] font-semibold bg-sky-50 text-sky-700 border border-sky-200">{{ $primaryCode ?? 'PRIM' }}</small>
              <span class="text-[11px] uppercase text-sky-700">En stock</span>
            </div>
            <div class="text-right font-semibold">{{ $print($card['primary']) }}</div>
          </div>
        </div>
      </div>
    @endforeach
  </div>
</div>
