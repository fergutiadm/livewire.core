@props([
  // Recibidos desde la clase:
  'cards' => [],
  'title' => 'Totales de inversión',
  'raw' => false,
  'primaryCode' => 'CUP',
])

@php
  $fmt    = fn($n) => number_format((float)$n, 2, '.', ',');   // bonito
  $fmtRaw = fn($n) => (string) (int) round((float)$n);         // crudo
  $print  = $raw ? $fmtRaw : $fmt;

  $chip = function(string $code) {
    return strtoupper($code) === 'USD'
      ? 'ml-1 px-1.5 py-0.5 rounded-md text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200'
      : 'ml-1 px-1.5 py-0.5 rounded-md text-[10px] font-semibold bg-sky-50 text-sky-700 border border-sky-200';
  };
@endphp

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
  <div class="text-center text-sm font-extrabold text-slate-800 tracking-wide uppercase mb-4">
    {{ $title }}
  </div>

  <div class="grid grid-cols-12 gap-4 items-stretch">
    @foreach($cards as $card)
      <div class="col-span-12 md:col-span-4">
        <div class="rounded-xl border border-slate-200 p-3 bg-slate-50/40 h-full flex flex-col justify-between">
          <div class="text-[11px] uppercase text-slate-500 mb-1">{{ $card['label'] }}</div>

          {{-- USD arriba si existe --}}
          @if(!is_null($card['usd']))
            <div class="font-semibold leading-tight mb-2">
              {{ $print($card['usd']) }}
              <small class="{{ $chip('USD') }}">USD</small>
            </div>
          @endif

          {{-- Moneda primaria debajo --}}
          <div class="font-semibold leading-tight">
            {{ $print($card['primary']) }}
            <small class="{{ $chip($card['primary_code'] ?? $primaryCode) }}">
              {{ $card['primary_code'] ?? $primaryCode }}
            </small>
          </div>
        </div>
      </div>
    @endforeach
  </div>
</div>
