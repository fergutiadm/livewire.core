<div>
    {{-- Contenedor de cards --}}
    <div class="space-y-4"
         x-data
         x-init="
            new Sortable($el, {
                group: 'cards',
                animation: 150,
                handle: '.drag-handle',
                onEnd: (evt) => {
                    const nuevoOrden = Array.from($el.children).map((child, index) => ({
                        order: index,
                        value: child.dataset.attrId
                    }));
                    $wire.call('reorderCards', nuevoOrden);
                }
            });
         "
    >
        @foreach($cards as $attrId => $card)
            <div class="p-4 border rounded shadow bg-white" data-attr-id="{{ $attrId }}">
                {{-- Header --}}
                <div class="flex justify-between items-center">
                    <span class="font-bold">
                        {{ $card['atributo'] }}
                        @if($card["sugerido"])
                        <span class="text-xs px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-800">
                            Valores Sugeridos
                        </span>
                        @endif
                    </span>
                    <span class="drag-handle cursor-move text-gray-400">≡</span>
                </div>

                {{-- Valores --}}
                <ul class="mt-2 space-y-1"
                    x-data
                    x-init="
                        new Sortable($el, {
                            group: 'valores',
                            animation: 100,
                            handle: '.drag-handle-valor',
                            onEnd: (evt) => {
                                const valoresOrden = Array.from($el.children).map((li, index) => ({
                                    order: index,
                                    value: li.dataset.value
                                }));
                                $wire.call('reorderValores', valoresOrden);
                            }
                        });
                    "
                >
                    @forelse($card['valores'] as $valorId => $valor)
                        <li class="flex justify-between items-center p-1 border rounded bg-gray-50"
                            data-value="{{ $attrId }}:{{ $valorId }}">
                            <span>{{ $valor }}</span>
                            <span class="drag-handle-valor cursor-move text-gray-400">≡</span>
                            <button type="button" wire:click="quitarValor({{ $attrId }}, {{ $valorId }})"
                                    class="text-red-500 text-xs ml-2 hover:text-red-700"
                                    title="Quitar valor">
                                ✕
                            </button>
                        </li>
                    @empty
                        <li class="p-2 bg-white border rounded text-gray-400 text-sm italic">
                            No hay valores aún
                        </li>
                    @endforelse
                </ul>
            </div>
        @endforeach
    </div>

    {{-- Guardar botones --}}
    @if($cardsDirty || $atributosSugeridos)
        <div class="mt-4 flex justify-end">
            <button type="button" class="btn-primary" wire:click="guardarCardsOrden">
                Guardar atributos
            </button>
            @php
                $classCheck = $cerrarModalAlSalvar ? 'text-green-600 font-italic' : 'text-orange-600 font-semibold';
            @endphp
            <label
            class="mx-4 text-sm {{ $classCheck }}"
            >
                <input type="checkbox" checked wire:click="cambiarCerrarModalAlSalvar">
                @if($cerrarModalAlSalvar)
                Cerrar al guardar
                @else
                Continuar editando
                @endif
            </label>
        </div>
    @endif
</div>
