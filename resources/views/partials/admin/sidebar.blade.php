{{-- resources/views/partials/admin/sidebar.blade.php --}}
@php
  use Illuminate\Support\Facades\Route;

  $make = function (string $name, string $route, string $icon, string $pattern) {
    return [
      'name'    => $name,
      'route'   => $route,
      'url'     => Route::has($route) ? route($route) : '#',
      'active'  => request()->routeIs($pattern),
      'icon'    => $icon,
    ];
  };

  $isAdmin = true;#auth()->check() && auth()->user()->hasRole('admin');

  $items = [];
  $items[] = $make('Panel',     'admin.index',            'home',        'admin.index');
  $items[] = $make('Locales',    'admin.locales',     'store',       'admin.locales');
  $items[] = $make('Categorías', 'admin.categorias', 'folder',      'admin.categorias');
  $items[] = $make('Productos',  'admin.productos',   'tag',         'admin.productos');
  $items[] = $make('Atributos',  'admin.atributos', 'adjustments', 'admin.atributos');
  $items[] = $make('Inventario', 'admin.inventarios',  'inventory',   'admin.inventarios');
  $items[] = $make('Monedas',    'admin.monedas', 'currency',    'admin.monedas');
  $items[] = $make('Tarjetas Magnéticas',    'admin.tarjetas_magneticas', 'card',    'admin.tarjetas_magneticas');
  $items[] = $make('Clientes',   'admin.clientes',    'clients',       'admin.clientes');
  $items[] = $make('Gestores',   'admin.gestores',    'users',       'admin.gestores');
  $items[] = $make('Periodos Contables', 'admin.periodos_contables', 'accounting_period', 'admin.periodos_contables');
  $items[] = $make('Usuarios',   'admin.usuarios',    'user-group',  'admin.usuarios');
  $items[] = $make('Trazas', 'admin.trazas', 'shield', 'admin.trazas');

  /*if ($isAdmin) {
      if (Route::has('zapatera.admin.users.index')) {
          $items[] = $make('Usuarios', 'zapatera.admin.users.index', 'shield', 'zapatera.admin.users.*');
      }

      // 👉 NUEVO: Settings (solo admin)
      if (Route::has('zapatera.admin.settings.edit')) {
          $items[] = $make('Configuración', 'zapatera.admin.settings.index', 'settings', 'zapatera.admin.settings.*');
      }
  }*/

  /*  $base   = 'group flex items-center gap-3 px-3 py-2 rounded-md text-base';
  $active = 'bg-indigo-50 text-indigo-700 border border-indigo-100 underline font-black text-lg';
  $idle   = 'text-slate-700 hover:bg-slate-50';

  $svg_class = 'h-5 w-5 text-slate-400 group-hover:text-slate-500';
  $svg_class_active = 'h-7 w-7 text-indigo-600';  */

  $base   = 'group flex items-center gap-3 px-3 py-2 rounded-md text-base';
  $active = 'bg-indigo-50 hover:bg-opacity-25 text-indigo-600 border border-indigo-100 underline font-black text-lg
           dark:bg-indigo-900/40 dark:text-slate-100 dark:border-indigo-700';
  $idle   = 'text-slate-600 hover:text-slate-100 hover:bg-slate-700 dark:hover:bg-slate-800 dark:hover:txt-slate-50';

  $svg_class = 'h-5 w-5';
  $svg_class_active = 'h-7 w-7';
@endphp

<nav class="space-y-1">
  @foreach ($items as $i)
    <a href="{{ $i['url'] }}" @class([$base, $i['active'] ? $active : $idle])>
      @switch($i['icon'])
        @case('home')
          <svg class="{{ $i['active'] ? $svg_class_active : $svg_class }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9.75L12 3l9 6.75M4.5 10.5V21h15V10.5"/></svg>
        @break

        @case('tag')
          <svg class="{{ $i['active'] ? $svg_class_active : $svg_class }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 7.5v3A2.25 2.25 0 006 12.75h3L21 21l-8.25-12.75v-3A2.25 2.25 0 0010.5 3.75h-3A3.75 3.75 0 003.75 7.5z"/></svg>
        @break

        @case('folder')
          <svg class="{{ $i['active'] ? $svg_class_active : $svg_class }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 7.5A2.25 2.25 0 014.5 5.25h4.818a2.25 2.25 0 011.59.66L12.75 7.5h6.75A2.25 2.25 0 0121.75 9.75v6A2.25 2.25 0 0119.5 18H4.5A2.25 2.25 0 012.25 15.75v-6z"/></svg>
        @break

        @case('adjustments')
          <svg class="{{ $i['active'] ? $svg_class_active : $svg_class }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 13.5V3.75M6 20.25v-3M12 10.5V3.75M12 20.25v-6M18 6.75V3.75M18 20.25v-9"/></svg>
        @break

        @case('currency')
          <svg class="{{ $i['active'] ? $svg_class_active : $svg_class }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.75v10.5M9 9.75h6M6.75 12A5.25 5.25 0 0012 17.25 5.25 5.25 0 0017.25 12 5.25 5.25 0 0012 6.75 5.25 5.25 0 006.75 12z"/></svg>
        @break

        @case('card')
          <svg class="{{ $i['active'] ? $svg_class_active : $svg_class }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75-10.5h16.5a1.5 1.5 0 011.5 1.5v10.5a1.5 1.5 0 01-1.5 1.5H3.75a1.5 1.5 0 01-1.5-1.5V5.25a1.5 1.5 0 011.5-1.5z" />
          </svg>
        @break

        @case('users')
          <svg class="{{ $i['active'] ? $svg_class_active : $svg_class }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7.5 14.25A4.5 4.5 0 0012 18.75a4.5 4.5 0 004.5-4.5M15.75 8.25A3.75 3.75 0 1112 4.5a3.75 3.75 0 013.75 3.75z"/></svg>
        @break

        @case('clients')
          <svg class="{{ $i['active'] ? $svg_class_active : $svg_class }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7.5 14.25A4.5 4.5 0 0012 18.75a4.5 4.5 0 004.5-4.5M15.75 8.25A3.75 3.75 0 1112 4.5a3.75 3.75 0 013.75 3.75z"/></svg>
        @break

        @case('inventory')
          <svg class="{{ $i['active'] ? $svg_class_active : $svg_class }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 7.5l-9-4.5-9 4.5 9 4.5 9-4.5zM12 12v9m0 0l9-4.5M12 21l-9-4.5"/></svg>
        @break

        @case('shield')
          <svg class="{{ $i['active'] ? $svg_class_active : $svg_class }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 3l7.5 3v6c0 4.97-3.36 8.94-7.5 10-4.14-1.06-7.5-5.03-7.5-10V6L12 3z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m9.5 12 1.5 1.5L14.25 10.5"/></svg>
        @break

        @case('store')
          <svg class="{{ $i['active'] ? $svg_class_active : $svg_class }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7h18M5 7l1.5-3h11L19 7m-1 0v10a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V7m3 6h6"/></svg>
        @break

        @case('accounting_period')
          <svg class="{{ $i['active'] ? $svg_class_active : $svg_class }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M7 3v3m10-3v3M4 9h16M6.5 21h11A2.5 2.5 0 0 0 20 18.5v-9A2.5 2.5 0 0 0 17.5 7h-11A2.5 2.5 0 0 0 4 9.5v9A2.5 2.5 0 0 0 6.5 21Z"/><path stroke-linecap="round" stroke-linejoin="round" d="m9.5 14.5 2 2 3.5-3.5"/></svg>
        @break

        @case('user-group')
            <svg class="{{ $i['active'] ? $svg_class_active : $svg_class }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-2.533-4.653 12.62 12.62 0 00-4.332-.682c-.683 0-1.353.054-2.008.157m-9.75 3.102c.535-1.33 1.532-2.428 2.793-3.091a12.645 12.645 0 0110.428 0c1.261.663 2.258 1.761 2.793 3.091m-18 1.92a9.345 9.345 0 018.535-5.917c.535 0 1.054.045 1.56.133m-1.44-10.8a3.976 3.976 0 00-2.094.58 3.976 3.976 0 00-2.094-.58 3.975 3.975 0 00-3.975 3.975c0 2.25 1.875 3.975 3.975 3.975s3.975-1.725 3.975-3.975c0-2.25-1.875-3.975-3.975-3.975z" />
            </svg>
        @break

        {{-- 👉 NUEVO: icono engranaje para Configuración --}}
        @case('settings')
          <svg class="{{ $i['active'] ? $svg_class_active : $svg_class }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6"
              d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.54-.89 3.31.88 2.419 2.42a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.065 2.573c.89 1.54-.88 3.31-2.42 2.419a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0A1.724 1.724 0 007.752 20.1c-1.54.89-3.31-.88-2.42-2.42a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35.489-.119.907-.405 1.166-.8.258-.394.34-.875.228-1.327-.89-1.54.88-3.31 2.42-2.42.452.112.933.03 1.327-.228.395-.259.681-.677.8-1.166z"/>
            <circle cx="12" cy="12" r="3" stroke-width="1.6"/>
          </svg>
        @break
      @endswitch

      <span class="{{ $i['active'] ? ' text-lg' : 'text-sm' }}">{{ $i['name'] }}</span>
    </a>
  @endforeach

  {{-- barra de monedas --}}
  {{--  <p>Barra Monedas</p>  --}}
  {{--  <x-currency.rates-bar decimals="4" variant="stacked" />  --}}
  {{--  <x-currency.rates-bar
    :currencies="$uiCurrencies"
    :codigo-principal="$primaryCode"
    decimals="4"
    variant="stacked"
    />  --}}


    <livewire:currency.rates-bar decimals="4" variant="stacked" showBoth="0" />
</nav>
