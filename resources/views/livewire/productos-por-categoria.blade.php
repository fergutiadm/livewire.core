<div class="max-w-7xl mx-auto px-4 py-6">

    {{-- Selector de Local --}}
    <div class="mb-6 flex gap-2 overflow-x-auto">
        <button wire:click="$set('local_id', null)"
                class="px-3 py-1 rounded-full border {{ $local_id === null ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700' }}">
            Todos los locales
        </button>

        @foreach($locales as $local)
            <button wire:click="setLocal({{ $local->id }})"
                    class="px-3 py-1 rounded-full border {{ $local_id === $local->id ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700' }}">
                {{ $local->nombre }}
            </button>
        @endforeach
    </div>

    {{-- Productos por categoría --}}
    @foreach($categorias as $categoria)
        @if($categoria->productos->count())
            <h2 class="text-xl font-bold mb-2">{{ $categoria->nombre }}</h2>

            <div class="swiper mySwiper-{{ $categoria->id }}">
                <div class="swiper-wrapper">
                    @foreach($categoria->productos as $producto)
                        <div class="swiper-slide bg-white rounded-lg shadow p-4 flex flex-col">
                            <img src="{{ $producto->imagen_url }}" alt="{{ $producto->nombre }}" class="h-48 w-full object-cover rounded mb-4">
                            <h3 class="text-lg font-semibold">{{ $producto->nombre }}</h3>
                            <p class="text-indigo-600 font-bold mt-1">${{ number_format($producto->precio, 2) }}</p>
                            <a href="/productos/{{ $producto->id }}"
                               class="mt-auto inline-block text-center bg-indigo-600 text-white py-2 rounded hover:bg-indigo-700">
                                Ver producto
                            </a>
                        </div>
                    @endforeach
                </div>
                {{-- Navegación --}}
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
            </div>

            {{-- Inicializar Swiper --}}
            <script>
                document.addEventListener('livewire:load', function () {
                    new Swiper(".mySwiper-{{ $categoria->id }}", {
                        slidesPerView: 1,
                        spaceBetween: 16,
                        navigation: {
                            nextEl: ".mySwiper-{{ $categoria->id }} .swiper-button-next",
                            prevEl: ".mySwiper-{{ $categoria->id }} .swiper-button-prev",
                        },
                        breakpoints: {
                            640: { slidesPerView: 2 },
                            768: { slidesPerView: 3 },
                            1024: { slidesPerView: 4 }
                        }
                    });
                });
            </script>

        @endif
    @endforeach
</div>
