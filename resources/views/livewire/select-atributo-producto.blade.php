<div class="space-y-6">

    {{-- Selector de atributo --}}
    <div class="panel-form-group">
        <x-label>Atributo – {{ ucfirst($modelType) }}</x-label>
        <x-select wire:model="atributoId" wire:change="atributoCambiado">
            <option value="" disabled>Seleccione</option>
            @foreach ($atributos as $attr)
                <option value="{{ $attr->id }}">{{ $attr->nombre }}</option>
            @endforeach
        </x-select>
    </div>

    {{-- Valores --}}
    @if(!empty($valores))
        <div class="panel-form-group">
            <ul class="space-y-1">
                @foreach ($valores as $valor)
                    <li>
                        <label class="flex items-center gap-2">
                            <x-checkbox
                                value="{{ $valor->id }}"
                                wire:model="valoresSeleccionados"
                                wire:key="valor-{{ $valor->id }}" />
                            {{ $valor->valor }}
                        </label>
                    </li>
                @endforeach
            </ul>

            <x-button
                class="mt-3"
                type="button"
                wire:click="agregarAtributo">
                Agregar atributo
            </x-button>
        </div>
    @endif
</div>
