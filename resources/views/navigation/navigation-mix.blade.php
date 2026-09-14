@props([
    'brand' => '',
])
<header x-data="{ open: false }" class="bg-white border-b sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 h-20 flex items-center justify-between">

        {{-- Logo --}}
        <div class="flex items-center gap-4 pt-3">
            {{--  <a href="/" class="font-bold text-lg text-gray-800">
                Core Store
            </a>  --}}
            <a href="{{ url('/') }}" class="flex items-center gap-2 font-semibold">
                <img src="/img/ferguti-logo-3.png" class="h-14 md:h-24 object-contain rounded" alt="">
                <span>{{ $brand }}</span>
              </a>
        </div>

        {{-- Navegación Desktop --}}
        <nav class="hidden md:flex items-center gap-6 text-sm font-medium">
            <a href="/productos" class="text-gray-600 hover:text-gray-900">Productos</a>
            <a href="/categorias" class="text-gray-600 hover:text-gray-900">Categorías</a>

            @can('registrar venta')
                <a href="/ventas" class="text-indigo-600 hover:text-indigo-800">Ventas</a>
            @endcan

            @can('ver promociones')
                <a href="/promociones" class="text-green-600 hover:text-green-800">Promociones</a>
            @endcan

            @can('ver periodos contables')
                <a href="/periodos" class="text-purple-600 hover:text-purple-800">Periodos</a>
            @endcan

            @can('ver inventario')
                <a href="/inventario" class="text-yellow-600 hover:text-yellow-800">Inventario</a>
            @endcan
        </nav>

        {{-- Usuario / Carrito / Botón móvil --}}
        <div class="flex items-center gap-4">

            {{-- Carrito --}}
            <div class="relative">
                <a href="/carrito" class="relative">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 9m5-9v9m4-9v9m5-9l2 9" />
                    </svg>
                    <livewire:cart-counter />
                </a>
            </div>

            {{-- Usuario o login --}}
            @auth
                <a href="/profile" class="text-sm text-gray-700">{{ auth()->user()->name }}</a>
            @else
                <a href="/login" class="text-sm text-gray-700">Entrar</a>
            @endauth

            {{-- Botón móvil --}}
            <button @click="open=!open" class="md:hidden text-gray-700 text-2xl">☰</button>
        </div>
    </div>

    {{-- Menú Mobile --}}
    <div x-show="open" x-transition class="md:hidden border-t bg-white">
        <nav class="flex flex-col">
            <a href="/productos" class="px-4 py-3">Productos</a>
            <a href="/categorias" class="px-4 py-3">Categorías</a>

            @can('registrar venta')
                <a href="/ventas" class="px-4 py-3 text-indigo-600">Ventas</a>
            @endcan

            @can('ver promociones')
                <a href="/promociones" class="px-4 py-3 text-green-600">Promociones</a>
            @endcan

            @can('ver periodos contables')
                <a href="/periodos" class="px-4 py-3 text-purple-600">Periodos</a>
            @endcan

            @can('ver inventario')
                <a href="/inventario" class="px-4 py-3 text-yellow-600">Inventario</a>
            @endcan

            {{-- Carrito Mobile --}}
            <a href="/carrito" class="px-4 py-3 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 9m5-9v9m4-9v9m5-9l2 9" />
                </svg>
                <livewire:cart-counter />
                <span>Carrito</span>
            </a>
        </nav>
    </div>
</header>
