@php
/** @var \Illuminate\Support\Collection $items */
/** @var string $primaryCode */
/** @var bool $showBoth */
/** @var string $variant */
/** @var int $decimals */
$items = $items ?? collect();
$decimals = $decimals ?? 4;
$showBoth = $showBoth ?? false;
$variant = $variant ?? 'stacked';
$others = $items->filter(fn($i) => !$i['es_principal']);

$primary = $items->firstWhere('es_principal', true);
$primaryBg = $primary['color_bg'] ?? 'bg-slate-900';
$primaryText = $primary['color_text'] ?? 'text-white';

//dump($primaryBg)
@endphp

{{-- DEBUG --}}
{{--  {{ dump($items) }}
echo "<hr>";
{{ dump($others) }}
echo "<hr>";  --}}

@if($items->isNotEmpty())
<div class="w-full rounded-2xl border bg-white px-3 py-2 text-sm text-slate-700">
    @if($variant === 'stacked')
        <div class="flex flex-col gap-2">
            <div class="inline-flex items-center gap-2">
                <span class="inline-flex items-center gap-1 rounded-full px-3 py-1
                    {{ $primaryBg }} {{ $primaryText }}">
                    <span class="font-semibold">{{ $codigoPrincipal }}</span>
                    <span class="text-xs opacity-80">base</span>
                </span>
                <span class="text-slate-400">•</span>
            </div>

            @foreach($others as $it)
            <div class="rounded-xl bg-slate-100 px-3 py-2">
                <div class="flex flex-col gap-1">
                    @php
                        $bg = $it['color_bg'] ?? 'bg-white';
                        $text = $it['color_text'] ?? 'text-slate-800';
                        //dump($bg)
                    @endphp
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs
                            {{ $bg }} {{ $text }}">
                            <span class="font-semibold">{{ $it['codigo'] }}</span>
                        </span>
                        <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[11px]
                            {{ $bg }} {{ $text }}">
                            <span>1 {{ $codigoPrincipal }}</span>
                            <span>=</span>
                            <span class="font-mono">{{ number_format($it['eq_per_primary'], $decimals) }}</span>
                            <span>{{ $it['codigo'] }}</span>
                        </span>
                    </div>

                    @if($showBoth)
                    <div class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[11px]
                            {{ $bg }} {{ $text }}">
                        <span>1 {{ $it['codigo'] }}</span>
                        <span>=</span>
                        <span class="font-mono">{{ number_format($it['tasa_cambio'], $decimals) }}</span>
                        <span>{{ $codigoPrincipal }}</span>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="flex gap-2 items-center whitespace-nowrap overflow-x-auto overscroll-x-contain snap-x scroll-px-2 pr-2">
            <span class="shrink-0 snap-start inline-flex items-center gap-1 rounded-full px-3 py-1
                    {{ $primaryBg }} {{ $primaryText }}">
                <span class="font-semibold">{{ $codigoPrincipal }}</span>
                <span class="text-xs opacity-80">base</span>
            </span>
            <span class="text-slate-400 shrink-0">•</span>

            @foreach($others as $it)
            <span class="shrink-0 snap-start inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1">
                <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs border
                        {{ $color($it['codigo'])['soft'] }}">
                    <span class="font-semibold">{{ $it['codigo'] }}</span>
                </span>
                <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs
                        {{ $bg }} {{ $text }}">
                    <span>1 {{ $codigoPrincipal }}</span>
                    <span>=</span>
                    <span class="font-mono">{{ number_format($it['eq_per_primary'], $decimals) }}</span>
                    <span>{{ $it['codigo'] }}</span>
                </span>

                @if($showBoth)
                <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs
                        {{ $bg }} {{ $text }}">
                    <span>1 {{ $it['codigo'] }}</span>
                    <span>=</span>
                    <span class="font-mono">{{ number_format($it['tasa_cambio'], $decimals) }}</span>
                    <span>{{ $codigoPrincipal }}</span>
                </span>
                @endif
            </span>
            @endforeach
        </div>
    @endif
</div>
@endif
