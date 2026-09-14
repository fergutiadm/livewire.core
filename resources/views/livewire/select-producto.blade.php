<div>
    <div class="mb-4">
        <x-label>Categoría</x-label>
        {{--  <x-select wire:model="categoriaId">
            @foreach ($categorias as $cat)
                <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
            @endforeach
        </x-select>  --}}
        <x-select class="w-full" wire:model="categoriaId" wire:change="cargarProductos">
            <option value="">Seleccione</option>
            @foreach ($categorias as $cat)
                <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
            @endforeach
        </x-select>
    </div>
    <div class="mb-4">
        <x-label>Producto</x-label>
        <x-select class="w-full" wire:model="productoId">
            @if(!$categoriaId)
                <option value="">Seleccione una Categoría</option>
            @endif
            @foreach ($productos as $prod)
                <option value="{{ $prod->id }}" wire:key="{{ $prod->id }}">{{ $prod->nombre }}</option>
            @endforeach
        </x-select>
    </div>
</div>
