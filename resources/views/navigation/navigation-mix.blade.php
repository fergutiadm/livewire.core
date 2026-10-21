{{-- resources/views/navigaction-mix.blade.php --}}
@props([
    'brand' => '',
])

@php
    $isAdmin = auth()->check() && auth()->user()->hasRole('admin');
@endphp

<header x-data="{ open: false }" class="bg-white border-b sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 h-20 flex items-center justify-between">

        {{-- Logo --}}
        <div class="flex items-center gap-4 pt-3">
            <a href="{{ url('/') }}" class="flex items-center gap-2 font-semibold">
                <img src="/img/ferguti-logo-3.png" class="h-14 md:h-24 object-contain rounded" alt="">
                <span>{{ $brand }}</span>
            </a>
        </div>

        {{-- Navegación Desktop --}}
        <nav class="hidden md:flex items-center gap-6 text-sm font-medium">
            @can('registrar venta') <a href="/ventas" class="text-indigo-600 hover:text-indigo-800">Ventas</a> @endcan
            @can('ver promociones') <a href="/promociones" class="text-sky-600 hover:text-sky-800">Promociones</a> @endcan
            {{-- @can('ver periodos contables') <a href="/periodos" class="text-purple-600 hover:text-purple-800">Periodos</a> @endcan --}}
            @can('ver inventario') <a href="/inventario" class="text-yellow-600 hover:text-yellow-800">Inventario</a> @endcan
            @can('ver gastos') <a href="/gastos" class="text-emerald-700 hover:text-emerald-800">Gastos</a> @endcan
        </nav>

        {{-- Bloque Derecho Superior --}}
        <div class="flex items-center gap-3">
            
            {{-- Selectores de Contexto (Solo visibles en PC/Tablets a partir de md) --}}
            {{-- @auth --}}
                <div class="hidden md:flex items-center gap-2">
                    <livewire:navigation.local-selector />
                </div>
            {{-- @endauth --}}

            {{-- Carrito (Oculto en móvil, visible en md) --}}
            <div class="hidden md:block relative">
                <a href="/carrito" class="relative block p-1">
                    <svg xmlns="http://w3.org" class="h-6 w-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 9m5-9v9m4-9v9m5-9l2 9" />
                    </svg>
                    <livewire:cart-counter />
                </a>
            </div>

            {{-- Nombre Usuario Desktop (Oculto en móvil) --}}
            @auth
                <a href="/profile" class="hidden md:block text-sm text-gray-700 font-medium hover:text-indigo-600 transition">{{ auth()->user()->name }}</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button type="submit"
                            class="hidden md:block group flex w-full items-center gap-3 px-3 py-2 bg-slate-700 rounded-md text-base
                                text-slate-300 hover:text-slate-100 hover:bg-slate-700
                                dark:hover:bg-slate-800">
                        <span class="text-sm">Cerrar sesión</span>
                    </button>
                </form>
                @if ($isAdmin)
                    <a href="/admin" class="hidden md:block text-sm text-gray-700 font-medium hover:text-indigo-600 transition">Configuración</a>
                @endif
            @else
                <a href="/login" class="hidden md:block text-sm text-gray-700 font-medium">Entrar</a>
            @endauth

            {{-- Botón menú móvil --}}
            <button @click="open = !open" class="md:hidden text-gray-700 text-2xl focus:outline-none p-1" aria-label="Menu">
                <span x-text="open ? '✕' : '☰'"></span>
            </button>
        </div>
    </div>

    {{-- Menú Desplegable Móvil --}}
    <div x-show="open" x-transition class="md:hidden border-t bg-white shadow-inner">
        <nav class="flex flex-col divide-y divide-slate-100 text-sm font-medium text-slate-700">
            @can('registrar venta') <a href="/ventas" class="px-4 py-3 text-indigo-600 hover:bg-slate-50">Ventas</a> @endcan
            @can('ver promociones') <a href="/promociones" class="px-4 py-3 text-green-600 hover:bg-slate-50">Promociones</a> @endcan
            @can('ver periodos contables') <a href="/periodos" class="px-4 py-3 text-purple-600 hover:bg-slate-50">Periodos</a> @endcan
            @can('ver inventario') <a href="/inventario" class="px-4 py-3 text-yellow-600 hover:bg-slate-50">Inventario</a> @endcan
            @can('ver gastos') <a href="/gastos" class="px-4 py-3 text-amber-800 hover:bg-slate-50">Gastos</a> @endcan

            <!-- Selectores de contexto apilados en móvil -->
            <div class="px-4 py-3 bg-slate-50 flex flex-col gap-2">
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Contexto de trabajo</div>
                <livewire:navigation.local-selector />
            </div>

            {{-- ELEMENTOS EXCLUSIVOS MÓVILES INYECTADOS --}}
            @auth
                {{-- <!-- Selectores de contexto apilados en móvil -->
                <div class="px-4 py-3 bg-slate-50 flex flex-col gap-2">
                    <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Contexto de trabajo</div>
                    <livewire:navigation.local-selector />
                </div> --}}

                <!-- Carrito Móvil -->
                <a href="/carrito" class="px-4 py-3 flex items-center justify-between hover:bg-slate-50 bg-white">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://w3.org" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 9m5-9v9m4-9v9m5-9l2 9" />
                        </svg>
                        <span>Mi Carrito</span>
                    </div>
                    <livewire:cart-counter />
                </a>

                <!-- Enlace de Perfil / Nombre -->
                <a href="/profile" class="px-4 py-3 flex items-center gap-2 hover:bg-slate-50 bg-white text-slate-900 font-semibold border-b">
                    <i class="bi bi-person-circle text-base text-slate-500"></i>
                    <span>{{ auth()->user()->name }}</span>
                </a>

                <!-- Cerrar sesion -->
                <form method="POST" action="{{ route('logout') }}" class="mt-3">
                    @csrf

                    <button type="submit"
                            class="group flex w-full items-center gap-3 px-3 py-2 rounded-md text-base
                                text-slate-600 hover:text-slate-100 hover:bg-slate-700
                                dark:hover:bg-slate-800">

                        <span class="text-sm">Cerrar sesión</span>
                    </button>
                </form>
                
                @if ($isAdmin)
                    <a href="/admin" class="px-4 py-3 flex items-center gap-2 hover:bg-slate-50 bg-white text-slate-900 font-semibold border-b">
                        <i class="bi bi-person-circle text-base text-slate-500"></i>
                        <span>Configuración</span>
                    </a>
                @endif

            @else
                <a href="/login" class="px-4 py-3 text-center text-indigo-600 bg-slate-50 font-bold">Entrar al sistema</a>
            @endauth
        </nav>
    </div>
</header>
