<div class="flex flex-wrap gap-4">

    @if(empty($cards))
        <p class="text-sm text-gray-500 w-full">
            No hay atributos seleccionados
        </p>
    @endif

    @foreach($cards as $atributoId => $card)
        <div
            class="w-full md:w-[calc(50%-0.5rem)] border rounded-lg p-4 bg-slate-50 shadow-sm"
            wire:key="card-{{ $atributoId }}"
        >

            {{-- Header --}}
            <div class="flex justify-between items-center mb-2">
                <h4 class="font-semibold">
                    {{ $card['atributo'] }}
                </h4>

                <button
                    type="button"
                    class="text-red-600 text-xs"
                    wire:click="eliminarCard({{ $atributoId }})">
                    Quitar
                </button>
            </div>

            {{-- Valores (DRAG & DROP) --}}
            <ul
                wire:sortable="reorderValores"
                wire:sortable.options="{ animation: 150 }"
                class="space-y-2"
            >
                @foreach($card['valores'] as $valorId => $valor)
                    <li
                        wire:sortable.item="{{ $atributoId }}:{{ $valorId }}"
                        wire:key="valor-{{ $atributoId }}-{{ $valorId }}"
                        class="flex justify-between items-center
                               p-2 bg-white border rounded cursor-move"
                    >
                        <span>{{ $valor }}</span>

                        <button
                            type="button"
                            class="text-gray-400 text-xs"
                            wire:click="quitarValor({{ $atributoId }}, {{ $valorId }})">
                            ✕
                        </button>
                    </li>
                @endforeach
            </ul>

        </div>
    @endforeach

    {{-- Footer --}}
    @if($cardsDirty || $atributosSugeridos)
        <div class="w-full mt-4 flex justify-end">
            <button
                type="button"
                class="btn-primary"
                wire:click="guardarCardsOrden">
                Guardar atributos
            </button>
        </div>
    @endif

</div>
