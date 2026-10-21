{{-- resources/views/components/productos-por-categorias.blade.php --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 bg-gray-50/50 min-h-screen">

    {{-- Cabecera y Selector de Local --}}
    <div class="mb-10 text-center md:text-left md:flex md:items-center md:justify-between border-b border-gray-200 pb-6">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight text-gray-900 sm:text-4xl">Explora Nuestro Catálogo</h1>
            <p class="mt-2 text-sm text-gray-500">Encuentra los mejores productos disponibles en nuestras sucursales.</p>
        </div>
        
        {{-- Selector de Local tipo Pestañas --}}
        <div class="mt-4 md:mt-0 flex gap-2 overflow-x-auto pb-2 scrollbar-none justify-start md:justify-end">
            <button wire:click="$set('local_id', null)"
                    class="whitespace-nowrap px-4 py-2 text-sm font-medium rounded-xl border transition-all duration-200 {{ $local_id === null ? 'bg-indigo-600 text-white border-indigo-600 shadow-md shadow-indigo-100' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}">
                📍 Todos los locales
            </button>

            @foreach($locales as $local)
                <button wire:click="setLocal({{ $local->id }})"
                        class="whitespace-nowrap px-4 py-2 text-sm font-medium rounded-xl border transition-all duration-200 {{ $local_id === $local->id ? 'bg-indigo-600 text-white border-indigo-600 shadow-md shadow-indigo-100' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}">
                    🏢 {{ $local->nombre }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- Secciones de Categorías --}}
    <div class="space-y-16">
        @forelse($categorias as $categoria)
            @if($categoria->productos->count())
                <div class="group/section relative" wire:key="cat-{{ $categoria->id }}">
                    
                    {{-- Título de Categoría --}}
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <span class="h-8 w-1 bg-indigo-600 rounded-full"></span>
                            <h2 class="text-2xl font-bold tracking-tight text-gray-900 group-hover/section:text-indigo-600 transition-colors">
                                {{ $categoria->nombre }}
                            </h2>
                            <span class="bg-gray-100 text-gray-600 text-xs px-2.5 py-0.5 rounded-full font-medium">
                                {{ $categoria->productos->count() }} prod.
                            </span>
                        </div>
                    </div>

                    {{-- Carrusel de Swiper --}}
                    <div class="relative px-2">
                        <div class="swiper mySwiper-{{ $categoria->id }} !pb-4">
                            <div class="swiper-wrapper">
                                @foreach($categoria->productos as $producto)
                                    <div class="swiper-slide bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col overflow-hidden group h-full">
                                        
                                        {{-- Contenedor de Imagen --}}
                                        <div class="relative overflow-hidden bg-gray-100 aspect-square">
                                            <img src="{{ $producto->imagen_url }}" 
                                                 alt="{{ $producto->nombre }}" 
                                                 class="h-full w-full object-cover object-center group-hover:scale-105 transition-transform duration-500">
                                            
                                            {{-- Distintivo de Precio flotante --}}
                                            <div class="absolute top-3 right-3 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full shadow-sm">
                                                <span class="text-indigo-600 font-bold text-base">
                                                    ${{ number_format($producto->precio, 2) }}
                                                </span>
                                            </div>
                                        </div>

                                        {{-- Información del Producto --}}
                                        <div class="p-5 flex flex-col flex-grow">
                                            <h3 class="text-gray-800 font-semibold text-base line-clamp-2 min-h-[3rem] group-hover:text-indigo-600 transition-colors">
                                                {{ $producto->nombre }}
                                            </h3>
                                            
                                            <a href="/productos/{{ $producto->id }}"
                                               class="mt-4 w-full text-center bg-gray-50 text-indigo-600 font-medium py-2.5 px-4 rounded-xl hover:bg-indigo-600 hover:text-white transition-all duration-200 border border-indigo-50/50 shadow-sm flex items-center justify-center gap-2">
                                                <span>Ver detalle</span>
                                                <svg xmlns="http://w3.org" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Botones de Navegación Personalizados fuera del Swiper --}}
                        <div class="swiper-button-prev-{{ $categoria->id }} absolute left-[-15px] top-1/2 -translate-y-1/2 z-10 w-9 h-9 bg-white border border-gray-200 rounded-full flex items-center justify-center shadow-md cursor-pointer hover:bg-indigo-50 text-gray-600 hover:text-indigo-600 transition-all">
                            ←
                        </div>
                        <div class="swiper-button-next-{{ $categoria->id }} absolute right-[-15px] top-1/2 -translate-y-1/2 z-10 w-9 h-9 bg-white border border-gray-200 rounded-full flex items-center justify-center shadow-md cursor-pointer hover:bg-indigo-50 text-gray-600 hover:text-indigo-600 transition-all">
                            →
                        </div>
                    </div>

                </div>
            @endif
        @empty
            <div class="text-center py-20 bg-white rounded-2xl border border-dashed border-gray-300">
                <p class="text-gray-500 text-lg">No se encontraron productos disponibles en este local.</p>
            </div>
        @endforelse
    </div>
</div>

{{-- Script compatible con Livewire 3 --}}
@script
<script>
    const initSwipers = () => {
        @foreach($categorias as $categoria)
            new Swiper(".mySwiper-{{ $categoria->id }}", {
                slidesPerView: 1,
                spaceBetween: 20,
                navigation: {
                    nextEl: ".swiper-button-next-{{ $categoria->id }}",
                    prevEl: ".swiper-button-prev-{{ $categoria->id }}",
                },
                breakpoints: {
                    640: { slidesPerView: 2 },
                    768: { slidesPerView: 3 },
                    1024: { slidesPerView: 4 }
                }
            });
        @endforeach
    };

    // Inicializar en la carga inicial
    initSwipers();

    // Reinicializar cada vez que Livewire actualice el DOM (por ejemplo al filtrar locales)
    $wire.on('post-render', () => {
        setTimeout(initSwipers, 50);
    });
</script>
@endscript
