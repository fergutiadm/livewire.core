@props([
    'moneda'        => null,
    'valorIncluido' => null,
    ])
<div>
    @if($moneda)
    <p class="block text-sm text-slate-800 flex items-center flex-wrap-0">

        @if($valorIncluido )
        <span class="text-indigo-600 font-bold mr-1">${{ number_format($valorIncluido, 2) }}</span>
        @endif

        <span class="truncate">{{ $moneda->codigo }}</span>

        <span
            @class([
                'text-xs font-semibold px-2 py-0.5 rounded-full ml-2 flex-shrink-0',
                $moneda->color_text ?? 'text-white',
                $moneda->color_bg ?? 'bg-indigo-500',
            ])>
            {{ $moneda->simbolo }}
        </span>

    </p>
    @endif
</div>
