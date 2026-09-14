<div class="max-w-7xl mx-auto px-4 py-6" wire:key="portada-lista-{{ $categoriaId }}">

    {{-- Selector de local --}}
    <div class="mb-3 flex gap-2 overflow-x-auto">
        <button wire:click="setLocal()"
                class="px-3 py-1 rounded-full border {{ $localId === null ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700' }}">
            Todos los locales
        </button>
        @foreach($locales as $local)
            <button wire:click="setLocal({{ $local->id }})"
                    class="px-3 py-1 rounded-full border {{ $localId === $local->id ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700' }}">
                {{ $local->nombre }}
            </button>
        @endforeach
    </div>

    {{-- Selector de categorías --}}
    <div
        class="mb-6 flex gap-2 overflow-x-auto"
        x-data="paginacionCategorias()"
        x-init="init()"
        x-ref="contenedor"
    >
        <button wire:click="setCategoria"
                class="px-3 py-1 rounded-full border {{ $categoriaId === null ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700' }}">
            Todas las categorías
        </button>
        @foreach($categorias_selector as $cat)
            <button wire:click="setCategoria({{ $cat->id }})"
                    class="px-3 py-1 rounded-full border {{ $categoriaId === $cat->id ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700' }}">
                {{ $cat->nombre }}
            </button>
        @endforeach
        @if($categorias_selector->hasPages())
            <div class="mx-5 bg-slate-200 border border-md border-slate-300 rounded-full">
                <x-ui.livewire.pagination :paginator="$categorias_selector" :border="1" :window="2" :show-summary="false" />
            </div>
        @endif
    </div>

    {{-- FILTROS SUPERIORES --}}
    <div class="flex flex-wrap gap-4 p-2 border-b border-slate-100 bg-slate-50/50 mb-6">
        <div class="flex-1 min-w-[200px]">
            <x-input wire:model.live="search" placeholder="Buscar por nombre o descripción..." class="w-full text-sm" />
        </div>
    </div>

    {{-- Productos por categoría con carousel horizontal --}}
    @foreach($categorias as $categoria)
        @if($categoria->productos_filtrados->count())
            <h2 class="text-xl font-bold mb-4 {{ $categoria->color_bg }} {{ $categoria->color_text }}">
                {{ $categoria->nombre }}
            </h2>

            <div
                x-data="carousel(@js($categoria->productos_filtrados))"
                x-init="init()"
                class="flex gap-4 overflow-x-auto snap-x snap-mandatory pb-4 carousel-container mb-8"
                id="categoria-{{ $categoria->id }}"
            >
                @foreach($categoria->productos_filtrados as $producto)
                    <div class="flex-none w-60 snap-start bg-white rounded-lg shadow p-4 flex flex-col hover:shadow-lg transition duration-300">

                        {{-- Imagen --}}
                        <img
                            src="{{ $producto->imagen_url ?? url('/placeholder/categoria/' . $categoria->id) . '?v=' . time() }}"
                            alt="{{ $producto->nombre }}"
                            class="h-48 w-full object-cover rounded mb-4"
                            loading="lazy"
                        >

                        {{-- Nombre --}}
                        <h3 class="text-lg font-semibold mb-1">{{ $producto->nombre }}</h3>

                        {{-- Descripción --}}
                        <p class="text-sm text-gray-600 mb-2">{{ $producto->descripcion }}</p>

                        {{-- Precio y Moneda --}}
                        <div class="flex items-center gap-2 text-sm mb-2">
                            <span class="text-indigo-600 font-bold">${{ number_format($producto->precio, 2) }}</span>
                            <span>{{ $producto->moneda_codigo ?? '' }}</span>
                            @if($producto->moneda_simbolo ?? false)
                                <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $producto->moneda_color_text ?? 'text-white' }} {{ $producto->moneda_color_bg ?? 'bg-indigo-500' }}">
                                    {{ $producto->moneda_simbolo }}
                                </span>
                            @endif
                        </div>

                        {{-- Stock y Oferta --}}
                        <div class="flex gap-2 mb-4">
                            @if($producto->stock == 0)
                                <span class="text-xs px-2 py-1 bg-red-500 text-white rounded-full">Agotado</span>
                            @endif
                            @if($producto->oferta)
                                <span class="text-xs px-2 py-1 bg-green-500 text-white rounded-full">Oferta</span>
                            @endif
                        </div>

                        {{-- Botón agregar --}}
                        <button wire:click="addToCart({{ $producto->id }})"
                                class="mt-auto bg-indigo-600 text-white py-2 rounded hover:bg-indigo-700 transition">
                            Agregar al carrito
                        </button>
                    </div>
                @endforeach
            </div>
        @endif
    @endforeach

</div>

{{-- Toast simple --}}
<script>
    window.addEventListener('toast', event => {
        alert(event.detail.message);
    });
</script>

{{-- Carousel Alpine (solo scroll horizontal, sin filtrar aquí, ya viene filtrado desde backend) --}}
<script>
function carousel(productos) {
    return {
        init() {
            // Nada extra: el scroll horizontal funciona por CSS "overflow-x-auto" y snap
        }
    }
}
</script>

<script>
    function paginacionCategorias() {
        return {
            anchoCard: 140,
            //anchoCard: 120,
            calcular() {
                if (!this.$refs.contenedor) return;
                const ancho = this.$refs.contenedor.offsetWidth || 0;
                let cantidad = Math.floor(ancho / this.anchoCard);

                const width = window.innerWidth;
                if (width >= 1280) cantidad = 5;
                else if (width >= 1024) cantidad = 3;
                else if (width >= 768) cantidad = 2;
                else cantidad = 1;

                if (this.$wire) this.$wire.dispatch('setPerPageCategorias', [cantidad]);
            },
            init() {
                this.calcular();
                let timeout;
                window.addEventListener('resize', () => {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => this.calcular(), 250);
                });
            }
        }
    }
</script>
