@php
  $brand   = config('app.name', 'GuineraStore');
  $isAdmin = request()->is('zapatera/*') || request()->is('zapatera');
  $containerClass = $isAdmin
      ? 'w-full'                               //admin: ocupa todo
      : 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8';
@endphp

<header class="sticky top-0 z-30 bg-white border-b border-slate-200">
  <div class="{{ $containerClass }}">
    <div class="h-14 flex items-center justify-between gap-2 px-3 sm:px-4">

      {{-- Izquierda: burger + marca --}}
      <div class="flex items-center gap-3 min-w-0">
        <button
            x-data
            type="button"
            class="lg:hidden inline-flex h-9 w-9 items-center justify-center rounded-md hover:bg-slate-100"
            @click="$dispatch('sidebar:toggle')"
            aria-label="Abrir menú"
        >
            <svg class="h-5 w-5 text-slate-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
      </button>

        <a href="{{ url('/') }}" class="flex items-center gap-2 min-w-0">
          <img src="/img/logo.png" class="h-6 w-6 rounded shrink-0" alt="">
          {{-- recorta en móvil para no pisar el botón derecho --}}
          <span class="font-semibold text-slate-900 truncate max-w-[60vw] sm:max-w-none">
            {{ $brand }}
          </span>
        </a>
      </div>

      {{-- Derecha: acciones --}}
      <div class="flex items-center gap-2 shrink-0">
        {{-- Catálogo: icono en xs, texto desde sm --}}
        <a href="{{ route('catalogo') }}"
            class="inline-flex items-center justify-center
                    h-9 w-9 rounded-full border border-slate-200 text-slate-700
                    hover:bg-slate-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-300
                    sm:w-auto sm:rounded-xl sm:px-3 sm:gap-2">
            <!-- shopping-bag minimal -->
            <svg viewBox="0 0 24 24" class="h-5 w-5 stroke-[1.6]" fill="none" stroke="currentColor" stroke-width="1.6">
                <path d="M6 8h12l-1 11a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2L6 8Z"/>
                <path d="M9 8V6a3 3 0 1 1 6 0v2"/>
            </svg>
            <span class="hidden sm:inline">Catálogo</span>
        </a>


        @auth
            <form method="POST" action="{{ route('logout') }}" class="shrink-0">
            @csrf
            <button type="submit" aria-label="Cerrar sesión"
                class="inline-flex items-center justify-center
                    h-9 w-9 rounded-full bg-slate-900 text-white
                    hover:bg-slate-900/90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-300
                    sm:w-auto sm:px-3 sm:gap-2 sm:rounded-xl">
                <svg viewBox="0 0 24 24" class="h-5 w-5 stroke-[1.6]" fill="none" stroke="currentColor">
                <path d="M15 12H3m0 0 4-4M3 12l4 4"/>
                <path d="M21 4v16a2 2 0 0 1-2 2h-6"/>
                </svg>
                <span class="hidden sm:inline">Cerrar sesión</span>
            </button>
            </form>
        @else
            <a href="{{ route('login') }}" aria-label="Iniciar sesión" class="shrink-0
            inline-flex items-center justify-center
            h-9 w-9 rounded-full bg-slate-900 text-white
            hover:bg-slate-900/90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-300
            sm:w-auto sm:px-3 sm:gap-2 sm:rounded-xl">
            <svg viewBox="0 0 24 24" class="h-5 w-5 stroke-[1.6]" fill="none" stroke="currentColor">
                <path d="M10 12h12m0 0-4 4m4-4-4-4"/>
                <path d="M3 4h9a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H3"/>
            </svg>
            <span class="hidden sm:inline">Iniciar sesión</span>
            </a>
        @endauth

      </div>

    </div>
  </div>
</header>
