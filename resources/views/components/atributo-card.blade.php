<div class="border rounded-lg p-4 bg-slate-50 shadow-sm w-full">
        <h4 class="font-semibold">{{ $card['atributo'] ?? '' }}</h4>
        @if($atributosSugeridos)
            <span class="text-xs text-gray-500 italic">
                sugerido
            </span>
        @endif
        <button
            type="button"
            class="text-red-600 text-xs"
            wire:click="$emitUp('eliminarCard', {{ $atributoId }})">
            Quitar
        </button>

    <ul class="space-y-1">
        @foreach($card['valores'] ?? [] as $valorId => $valor)
            <li class="flex justify-between items-center">
                <span><pre>{{ json_encode($card['valores'] ?? [], JSON_PRETTY_PRINT) }}</pre></span>

                <button
                    type="button"
                    class="text-gray-500 text-xs"
                    wire:click="$emitUp('quitarValor', {{ $atributoId }}, {{ $valorId }})">
                    ✕
                </button>
            </li>
        @endforeach
    </ul>
</div>
