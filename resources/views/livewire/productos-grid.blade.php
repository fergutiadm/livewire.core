<div class="max-w-7xl mx-auto px-4 py-6">

    {{-- Filtro de categorías --}}
    <div class="mb-6 flex gap-2 overflow-x-auto">
        <button wire:click="$set('categoria_id', null)"
                class="px-3 py-1 rounded-full border {{ $categoria_id === null ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700' }}">
            Todas
        </button>

        @foreach($categorias as $categoria)
            <button wire:click="setCategoria({{ $categoria->id }})"
                    class="px-3 py-1 rounded-full border {{ $categoria_id === $categoria->id ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700' }}">
                {{ $categoria->nombre }}
            </button>
        @endforeach
    </div>

    {{-- Grid de productos --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @forelse($productos as $producto)
            <div class="bg-white rounded-lg shadow p-4 flex flex-col">
                <img src="{{ $producto->imagen_url }}" alt="{{ $producto->nombre }}" class="h-48 w-full object-cover rounded mb-4">

                <h3 class="text-lg font-semibold">{{ $producto->nombre }}</h3>
                <p class="text-indigo-600 font-bold mt-1">${{ number_format($producto->precio, 2) }}</p>

                <a href="/productos/{{ $producto->id }}"
                   class="mt-auto inline-block text-center bg-indigo-600 text-white py-2 rounded hover:bg-indigo-700">
                    Ver producto
                </a>
            </div>
        @empty
            <p class="col-span-full text-center text-gray-500">No hay productos disponibles.</p>
        @endforelse
    </div>
</div>
