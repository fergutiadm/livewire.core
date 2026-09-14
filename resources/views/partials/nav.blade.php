{{-- NAVBAR --}}
<nav class="nlx-navbar">
    <div class="container flex h-16 items-center justify-between">
      <a href="{{ url('/') }}" class="flex items-center gap-2">
        <img src="{{ asset('img/logo-five.png') }}" alt="Logo" class="rounded" width="28" height="28">
        <span class="font-semibold text-blue-600/25 dark:text-sky-400/25">{{ $brand }}</span>
      </a>

      <ul class="hidden md:flex items-center gap-4">
        <li><a class="nlx-navlink" href="{{ route('catalogo') }}">Catálogo</a></li>
        <li><a class="nlx-navlink" href="{{ route('zapatera.admin') }}">Admin</a></li>
        <li><a class="nlx-navlink" href="{{ route('zapatera.admin.inventory.index') }}">Inventario</a></li>
        <li><a class="nlx-navlink" href="{{ route('zapatera.admin.gestors.index') }}">Gestores</a></li>
        <li><a class="nlx-btn-accent" href="#cta">WhatsApp</a></li>
      </ul>

      <button id="nlx-burger" class="md:hidden inline-flex items-center p-2 rounded hover:bg-gray-100">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
      </button>
    </div>

    <div id="nlx-mobile" class="md:hidden hidden border-t border-base-border">
      <ul class="container py-2 flex flex-col gap-1">
        <li><a class="nlx-navlink block py-2" href="{{ route('catalogo') }}">Catálogo</a></li>
        <li><a class="nlx-navlink block py-2" href="{{ route('zapatera.admin.panel') }}">Admin</a></li>
        <li><a class="nlx-navlink block py-2" href="{{ route('zapatera.admin.inventory.index') }}">Inventario</a></li>
        <li><a class="nlx-navlink block py-2" href="{{ route('zapatera.admin.gestors.index') }}">Gestores</a></li>
        <li><a class="nlx-btn-accent mt-1 w-max" href="#cta">WhatsApp</a></li>
      </ul>
    </div>
  </nav>