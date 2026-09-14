<div>
    @if($items->isNotEmpty())
        <div class="w-full rounded-2xl border shadow-xl shadow-black bg-white bg-opacity-10 px-3 py-2 text-sm text-slate-700">

            {{-- Variante stacked --}}
            @if($variant === 'stacked')
                <div class="flex flex-col gap-2">
                    {{-- Moneda principal --}}
                    <div class="inline-flex items-center gap-2">
                        <span class="inline-flex items-center gap-1 rounded-full px-3 py-1
                            {{ $primary['color_bg'] ?? 'bg-slate-900' }} {{ $primary['color_text'] ?? 'text-white' }}">
                            <span class="font-semibold">{{ $primary['codigo'] ?? '—' }}</span>
                            <span class="text-xs opacity-80">base</span>
                        </span>
                        <span class="text-slate-400">•</span>
                    </div>

                    {{-- Otras monedas --}}
                    @foreach($others as $it)
                        <div class="rounded-xl bg-slate-100 bg-opacity-45 px-3 py-2">
                            <div class="flex flex-col gap-1">
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs
                                        {{ $it['color_bg'] ?? 'bg-white' }} {{ $it['color_text'] ?? 'text-slate-800' }}">
                                        <span class="font-semibold">{{ $it['codigo'] }}</span>
                                    </span>

                                    <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[11px]
                                        {{ $it['color_bg'] ?? 'bg-white' }} {{ $it['color_text'] ?? 'text-slate-800' }}">
                                        <span>1 {{ $it['codigo'] ?? '—' }}</span>
                                        <span>=</span>
                                        <span class="font-mono">{{ number_format($it['eq_per_primary'], $decimals) }}</span>
                                        <span>{{ $primary['codigo'] }}</span>
                                    </span>
                                </div>

                                @if($showBoth)
                                    <div class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[11px]
                                        {{ $it['color_bg'] ?? 'bg-white' }} {{ $it['color_text'] ?? 'text-slate-800' }}">
                                        <span>1 {{ $primary['codigo'] }}</span>
                                        <span>=</span>
                                        <span class="font-mono">{{ number_format($it['inverse'], $decimals) }}</span>
                                        <span>{{ $it['codigo'] ?? '—' }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

            @else
                {{-- Variante horizontal --}}
                <div class="flex gap-2 items-center whitespace-nowrap overflow-x-auto overscroll-x-contain snap-x scroll-px-2 pr-2">
                    <span class="shrink-0 snap-start inline-flex items-center gap-1 rounded-full px-3 py-1
                            {{ $primary['color_bg'] ?? 'bg-slate-900' }} {{ $primary['color_text'] ?? 'text-white' }}">
                        <span class="font-semibold">{{ $primary['codigo'] ?? '—' }}</span>
                        <span class="text-xs opacity-80">base</span>
                    </span>
                    <span class="text-slate-400 shrink-0">•</span>

                    @foreach($others as $it)
                        <span class="shrink-0 snap-start inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1">
                            <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs
                                    {{ $it['color_bg'] ?? 'bg-white' }} {{ $it['color_text'] ?? 'text-slate-800' }}">
                                <span class="font-semibold">{{ $it['codigo'] }}</span>
                            </span>
                            <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs">
                                <span>1 {{ $it['codigo'] ?? '—' }}</span>
                                <span>=</span>
                                <span class="font-mono">{{ number_format($it['eq_per_primary'], $decimals) }}</span>
                                <span>{{ $primary['codigo'] }}</span>
                            </span>

                            @if($showBoth)
                                <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs">
                                    <span>1 {{ $primary['codigo'] }}</span>
                                    <span>=</span>
                                    <span class="font-mono">{{ number_format($it['inverse'], $decimals) }}</span>
                                    <span>{{ $it['codigo'] ?? '—' }}</span>
                                </span>
                            @endif
                        </span>
                    @endforeach
                </div>
            @endif
        </div>
    @endif
</div>
