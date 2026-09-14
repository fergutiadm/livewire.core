@php
  $brand = config('app.name', 'Core-LiveWire');
@endphp
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      class="h-full bg-slate-50 dark:bg-slate-900"
      x-data="{ dark: localStorage.theme === 'dark' }"
      x-init="$watch('dark', val => {
          localStorage.theme = val ? 'dark' : 'light';
          document.documentElement.classList.toggle('dark', val);
      });
      document.documentElement.classList.toggle(
          'dark',
          localStorage.theme === 'dark'
      );">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? $brand }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Favicon clásico .ico primero -->
    <link rel="icon" type="image/x-icon" href="{{ asset('img/favicon.ico') }}?v=1">
    <link rel="shortcut icon" href="{{ asset('img/favicon.ico') }}">

    <!-- PNG (otros navegadores) -->
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('img/favicon-96x96.png') }}">

    <!-- SVG (navegadores modernos) -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('img/favicon.svg') }}">

    <!-- Apple Touch Icon (iOS) -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('img/apple-touch-icon.png') }}">

    <!-- Manifest para PWA -->
    <link rel="manifest" href="{{ asset('img/site.webmanifest') }}">


    @vite(['resources/css/app.css','resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles
</head>
<body class="h-full bg-slate-50 text-slate-800 dark:bg-slate-900 dark:text-slate-100">

  <!-- Barra superior -->
  <header class="
                    sticky top-0 z-30
                  bg-slate-500/70
                    border-b
                  border-slate-200


                  dark:bg-slate-800/80
                  dark:border-slate-700
                ">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-14 md:h-24 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <button x-data @click="$dispatch('sidebar:toggle')"
                class="lg:hidden inline-flex items-center justify-center rounded-md p-2 hover:bg-slate-100">
          <!-- Icono hamburguesa -->
          <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5"/>
          </svg>
        </button>
        <a href="{{ url('/') }}" class="flex items-center gap-2 font-semibold">
          <img src="/img/ferguti-logo-3.png" class="h-14 md:h-24 object-contain rounded" alt="">
          <span>{{ $brand }}</span>
        </a>
      </div>

      <div class="flex items-center gap-3">
        @auth
          <span class="
                        hidden sm:block text-sm text-slate-600

                        dark:text-slate-100
                      ">{{ auth()->user()->name }}</span>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-sm px-3 py-1.5 rounded-md bg-slate-900 text-white hover:bg-slate-800">
              Cerrar sesión
            </button>
            <button type="button" @click="dark = !dark"
                    class="px-2 py-1 rounded-md hover:bg-slate-200 dark:hover:bg-slate-700">

                <!-- Sol -->
                <svg x-show="dark" class="h-5 w-5" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M12 4.5v1.5m0 12v1.5m7.5-7.5h-1.5M6 12H4.5m12.02 5.52-1.06-1.06M8.54 8.54 7.48 7.48m8.04 0-1.06 1.06M8.54 15.46l-1.06 1.06M12 8.25A3.75 3.75 0 1 1 12 15.75 3.75 3.75 0 0 1 12 8.25Z"/>
                </svg>

                <!-- Luna -->
                <svg x-show="!dark" class="h-5 w-5" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M21 12.79A9 9 0 1 1 11.21 3c0 .34.02.67.06 1A7 7 0 0 0 20 12.73c.33.04.66.06 1 .06Z"/>
                </svg>

            </button>

          </form>
        @else
          <a href="{{ route('login') }}" class="text-sm px-3 py-1.5 rounded-md bg-slate-900 text-white hover:bg-slate-800">
            Iniciar sesión
          </a>
        @endauth
      </div>
    </div>
  </header>

  <!-- Shell de 2 columnas: sidebar fijo + contenido -->
  <div class="min-h-[calc(100vh-56px)] lg:grid lg:grid-cols-[18rem_minmax(0,1fr)]">

    {{-- Sidebar --}}
    <aside x-data="{open:false}"
           @sidebar:toggle.window="open=!open"
           class="bg-indigo-800  bg-opacity-25 border-r border-slate-200 lg:static lg:translate-x-0
                  fixed inset-y-0 left-0 z-40 w-72 transform transition-transform duration-200
                  -translate-x-full lg:w-[18rem]
                 "
           :class="{'translate-x-0': open}">
      <div class="h-14 lg:hidden"></div>
      <div class="h-full overflow-y-auto px-4 py-4">
        <div class="text-xs font-semibold text-slate-200 mb-3">Navegación</div>
        @include('partials.admin.sidebar')
      </div>
    </aside>

    {{-- Contenido --}}
    <main class="px-4 sm:px-6 lg:px-8 py-6
             bg-slate-50 dark:bg-slate-900">
      @yield('content')
      {{ $slot }}
    </main>
  </div>

        @stack('modals')

        <x-toasts />

        @livewireScripts

</body>
</html>
