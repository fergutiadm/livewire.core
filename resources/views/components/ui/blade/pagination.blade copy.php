@props([
  /** @var \Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Contracts\Pagination\Paginator $paginator */
  'paginator',
  'window' => 1,          // cuántas páginas a cada lado de la actual
  'showSummary' => true,  // mostrar "Mostrando X–Y de Z"
])

@php
  $p = $paginator;
  $current = $p->currentPage();
  $last    = method_exists($p, 'lastPage') ? $p->lastPage() : $current; // safety

  // Rango con elipsis
  $w = (int) $window;
  $start = max(1, $current - $w);
  $end   = min($last, $current + $w);

  $pages = [];
  if ($start > 1) {
    $pages[] = 1;
    if ($start > 2) $pages[] = '...';
  }
  for ($i = $start; $i <= $end; $i++) $pages[] = $i;
  if ($end < $last) {
    if ($end < $last - 1) $pages[] = '...';
    $pages[] = $last;
  }
@endphp

@if($p->hasPages())
<div class="mt-6">
  <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6 rounded-lg">

    {{-- Mobile: Prev/Next --}}
    <div class="flex flex-1 justify-between sm:hidden">
      <a href="{{ $p->onFirstPage() ? '#' : $p->previousPageUrl() }}"
         class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium
                {{ $p->onFirstPage() ? 'text-gray-400 pointer-events-none' : 'text-gray-700 hover:bg-gray-50' }}">
        Anterior
      </a>
      <a href="{{ $p->hasMorePages() ? $p->nextPageUrl() : '#' }}"
         class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium
                {{ $p->hasMorePages() ? 'text-gray-700 hover:bg-gray-50' : 'text-gray-400 pointer-events-none' }}">
        Siguiente
      </a>
    </div>

    {{-- Desktop --}}
    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
      <div class="text-sm text-gray-700">
        @if($showSummary)
          @if(method_exists($p, 'total') && $p->total() > 0)
            Mostrando <span class="font-medium">{{ $p->firstItem() }}</span>
            a <span class="font-medium">{{ $p->lastItem() }}</span>
            de <span class="font-medium">{{ $p->total() }}</span> resultados
          @else
            Sin resultados
          @endif
        @endif
      </div>

      <div>
        <nav aria-label="Pagination" class="isolate inline-flex -space-x-px rounded-md shadow-xs">
          {{-- Prev --}}
          <a href="{{ $p->onFirstPage() ? '#' : $p->previousPageUrl() }}"
             class="relative inline-flex items-center rounded-l-md px-2 py-2
                    {{ $p->onFirstPage() ? 'text-gray-300 pointer-events-none' : 'text-gray-400 hover:bg-gray-50 inset-ring inset-ring-gray-300' }}
                    focus:z-20 focus:outline-offset-0">
            <span class="sr-only">Anterior</span>
            <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" class="size-5">
              <path fill-rule="evenodd" clip-rule="evenodd"
                    d="M11.78 5.22a.75.75 0 0 1 0 1.06L8.06 10l3.72 3.72a.75.75 0 1 1-1.06 1.06l-4.25-4.25a.75.75 0 0 1 0-1.06l4.25-4.25a.75.75 0 0 1 1.06 0Z"/>
            </svg>
          </a>

          {{-- Páginas / elipsis --}}
          @foreach($pages as $num)
            @if($num === '...')
              <span class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 inset-ring inset-ring-gray-300">…</span>
            @else
              <a href="{{ $p->url($num) }}"
                 @class([
                   'relative inline-flex items-center px-4 py-2 text-sm font-semibold focus:z-20',
                   'z-10 bg-indigo-600 text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600' => $num === $current,
                   'text-gray-900 inset-ring inset-ring-gray-300 hover:bg-gray-50 focus:outline-offset-0' => $num !== $current,
                   // oculta páginas lejanas en md- para no saturar
                   'hidden md:inline-flex' => !in_array($num, [$current-1, $current, $current+1, 1, $last])
                 ])>
                {{ $num }}
              </a>
            @endif
          @endforeach

          {{-- Next --}}
          <a href="{{ $p->hasMorePages() ? $p->nextPageUrl() : '#' }}"
             class="relative inline-flex items-center rounded-r-md px-2 py-2
                    {{ $p->hasMorePages() ? 'text-gray-400 hover:bg-gray-50 inset-ring inset-ring-gray-300' : 'text-gray-300 pointer-events-none' }}
                    focus:z-20 focus:outline-offset-0">
            <span class="sr-only">Siguiente</span>
            <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" class="size-5">
              <path fill-rule="evenodd" clip-rule="evenodd"
                    d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z"/>
            </svg>
          </a>
        </nav>
      </div>
    </div>
  </div>
</div>
@endif
