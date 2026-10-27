{{-- resources/views/livewire/productos-por-categoria-final.blade.php --}}
<div class="max-w-7xl mx-auto px-4 py-6" wire:key="portada-lista-{{ $categoriaId }}">

    {{-- Selector de local --}}
    <div class="mb-3 flex gap-2 overflow-x-auto">
        {{-- <button wire:click="setLocal()"
                class="px-3 py-1 rounded-full border {{ $localId === null ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700' }}">
            Todos los locales
        </button> --}}
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

            @if (!$categoriaId)
            <div
                x-data="carousel(@js($categoria->productos_filtrados))"
                x-init="init()"
                class="flex gap-4 overflow-x-auto snap-x snap-mandatory pb-4 carousel-container mb-8"
                id="categoria-{{ $categoria->id }}"
            >
            @else
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
            @endif

            @foreach($categoria->productos_filtrados as $producto)
                @php
                    $agotado = (float) ($producto->stock ?? 0) <= 0;
                @endphp

                <article
                    wire:key="producto-card-{{ $producto->id }}"
                    @class([
                        'group flex h-full min-w-0 flex-col overflow-hidden rounded-2xl',
                        'border border-slate-200/80 bg-white shadow-sm',
                        'transition duration-200 hover:-translate-y-0.5 hover:shadow-lg',
                        'w-60 flex-none snap-start' => !$categoriaId,
                        'w-full' => $categoriaId,
                    ])
                >
                    {{-- Imagen: altura idéntica en todas las tarjetas --}}
                    <div class="relative h-48 shrink-0 overflow-hidden bg-slate-100">
                        <img
                            src="{{ $producto->imagen_url ?? url('/placeholder/categoria/' . $categoria->id) }}"
                            alt="{{ $producto->nombre }}"
                            class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.03]"
                            loading="lazy"
                        >

                        {{-- Estado del producto --}}
                        <div class="absolute left-3 top-3 flex flex-wrap gap-1.5">
                            @if($agotado)
                                <span class="inline-flex items-center gap-1 rounded-full bg-red-600/95 px-2.5 py-1 text-xs font-semibold text-white shadow-sm">
                                    <span class="h-1.5 w-1.5 rounded-full bg-white"></span>
                                    Agotado
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-600/95 px-2.5 py-1 text-xs font-semibold text-white shadow-sm">
                                    <span class="h-1.5 w-1.5 rounded-full bg-white"></span>
                                    Disponible
                                </span>
                            @endif

                            @if($producto->oferta)
                                <span class="rounded-full bg-amber-400 px-2.5 py-1 text-xs font-bold text-amber-950 shadow-sm">
                                    Oferta
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Contenido con secciones de altura uniforme --}}
                    <div class="flex flex-1 flex-col p-4">

                        {{-- Nombre: reserva espacio para dos líneas --}}
                        <h3
                            class="h-12 overflow-hidden text-base font-semibold leading-6 text-slate-800"
                            style="display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 2;"
                            title="{{ $producto->nombre }}"
                        >
                            {{ $producto->nombre }}
                        </h3>

                        {{-- Descripción: reserva espacio para tres líneas --}}
                        <p
                            class="mt-1 h-[3.75rem] overflow-hidden text-sm leading-5 text-slate-500"
                            style="display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 3;"
                            title="{{ $producto->descripcion }}"
                        >
                            {{ $producto->descripcion ?: 'Sin descripción disponible.' }}
                        </p>

                        {{-- Precio: siempre en la misma posición relativa --}}
                        <div class="mt-3 flex min-h-10 items-center justify-between gap-2 border-t border-slate-100 pt-3">
                            <div class="min-w-0">
                                <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">
                                    Precio
                                </p>

                                <p class="truncate text-lg font-bold leading-6 text-indigo-700">
                                    ${{ number_format($producto->precio, 2) }}
                                </p>
                            </div>

                            @if($producto->moneda_simbolo)
                                <span
                                    class="shrink-0 rounded-lg px-2 py-1 text-xs font-semibold {{ $producto->moneda_color_text ?? 'text-white' }} {{ $producto->moneda_color_bg ?? 'bg-indigo-500' }}"
                                    title="{{ $producto->moneda_codigo }}"
                                >
                                    {{ $producto->moneda_simbolo }}
                                </span>
                            @elseif($producto->moneda_codigo)
                                <span class="shrink-0 rounded-lg bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-600">
                                    {{ $producto->moneda_codigo }}
                                </span>
                            @endif
                        </div>

                        {{-- Acción: alineada al fondo de la tarjeta --}}
                        <div class="mt-auto pt-4">
                            <button
                                type="button"
                                wire:click="addToCart({{ $producto->id }})"
                                @disabled($agotado)
                                class="flex min-h-11 w-full items-center justify-center gap-2 rounded-xl px-3 py-2.5 text-sm font-semibold transition
                                    {{ $agotado
                                        ? 'cursor-not-allowed bg-slate-100 text-slate-400'
                                        : 'bg-indigo-600 text-white shadow-sm hover:bg-indigo-700 active:scale-[0.98]' }}"
                            >
                                @if($agotado)
                                    <i class="bi bi-cart-x"></i>
                                    No disponible
                                @else
                                    <i class="bi bi-cart-plus"></i>
                                    Agregar al carrito
                                @endif
                            </button>
                        </div>
                    </div>
                </article>
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
