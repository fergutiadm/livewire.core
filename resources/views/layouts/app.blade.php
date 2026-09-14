<!DOCTYPE html>
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
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Core-Livewire') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Favicon clásico .ico primero -->
        <link rel="icon" type="image/x-icon" href="{{ asset('img/favicon.ico') }}?v=1.1">
        <link rel="shortcut icon" href="{{ asset('img/favicon.ico') }}">

        <!-- PNG (otros navegadores) -->
        <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('img/favicon-96x96.png') }}?v=1.1">

        <!-- SVG (navegadores modernos) -->
        <link rel="icon" type="image/svg+xml" href="{{ asset('img/favicon.svg') }}?v=1.1">

        <!-- Apple Touch Icon (iOS) -->
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('img/apple-touch-icon.png') }}?v=1.1">

        <!-- Manifest para PWA -->
        <link rel="manifest" href="{{ asset('img/site.webmanifest') }}">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles
    </head>
    <body class="font-sans antialiased">
        <x-banner />

        <div class="min-h-screen bg-gray-100">
            {{--  @livewire('navigation-menu')  --}}
            {{--  @include('navigation.navigation-jetstream-friendly')  --}}
            {{--  @include('navigation.navigation-tabs')  --}}
            {{--  @include('navigation.navigation-responsive')  --}}
            {{--  @include('navigation.navigation-admin-pro')  --}}

            @include('navigation.navigation-mix')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        @stack('modals')

        <x-toasts />

        @livewireScripts
    </body>
</html>
