@props([
  'paginator',
  'window' => 1,
  'showSummary' => true,
  'border' => 0,
])

@php
  $p = $paginator;
  $current = $p->currentPage();
  $last    = method_exists($p, 'lastPage') ? $p->lastPage() : $current;

  $w = (int) $window;
  $start = max(1, $current - $w);
  $end   = min($last, $current + $w);

  $pages = [];
  if ($start > 1) { $pages[] = 1; if ($start > 2) $pages[] = '...'; }
  for ($i=$start; $i<=$end; $i++) $pages[] = $i;
  if ($end < $last) { if ($end < $last-1) $pages[] = '...'; $pages[] = $last; }

  $border_btn_prev_next_class = ['','rounded-full', 'rounded-sm', 'roundend-md', 'rounded-lg'];
  $border_btn_prev_class = ['','rounded-l-full', 'rounded-l-sm', 'roundend-l-md', 'rounded--llg'];
  $border_btn_next_class = ['','rounded-r-full', 'rounded-r-sm', 'roundend-r-md', 'rounded-r-lg'];
@endphp

<div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-3 md:space-y-0 rounded-s">

    {{-- IZQUIERDA: Información de resultados --}}
    @if($showSummary)
    <div class="text-sm text-gray-700">
        @if(isset($p) && method_exists($p, 'total') && $p->total() > 0)
            Mostrando
            <span class="font-medium">{{ $p->firstItem() }}</span>
            a
            <span class="font-medium">{{ $p->lastItem() }}</span>
            de
            <span class="font-medium">{{ $p->total() }}</span> resultados
        @else
            Sin resultados
        @endif
    </div>
    @endif

    {{-- CENTRO: slot opcional "middle" --}}
    <div class="flex justify-center">
        @isset($middle)
            {{ $middle }}
        @endisset
    </div>

    {{-- DERECHA: Paginación --}}
    <div class="flex justify-end">
        @if(isset($pages) && count($pages) > 0)
            <nav aria-label="Paginación" class="isolate inline-flex -space-x-px rounded-md shadow-sm">

                {{-- Botón Anterior --}}
                <button
                    wire:click="previousPage"
                    @disabled($p->onFirstPage())
                    class="relative inline-flex items-center {{ $border_btn_prev_class[$border] ?? 'rounded-l-md' }} px-2 py-2
                           {{ $p->onFirstPage() ? 'text-gray-300 pointer-events-none' : 'text-gray-400 hover:bg-gray-50' }}">
                    <span class="sr-only">Anterior</span>
                    <svg viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                              d="M11.78 5.22a.75.75 0 0 1 0 1.06L8.06 10l3.72 3.72a.75.75 0 1 1-1.06 1.06l-4.25-4.25a.75.75 0 0 1 0-1.06l4.25-4.25a.75.75 0 0 1 1.06 0Z"/>
                    </svg>
                </button>

                {{-- Botones de Páginas --}}
                @foreach($pages as $num)
                    @if($num === '...')
                        <span class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700">…</span>
                    @else
                        <button
                            wire:click="gotoPage({{ $num }})"
                            @class([
                                'relative inline-flex items-center px-4 py-2 text-sm font-semibold',
                                'z-10 bg-indigo-600 text-white' => $num === $current,
                                'text-gray-900 hover:bg-gray-50' => $num !== $current,
                            ])>
                            {{ $num }}
                        </button>
                    @endif
                @endforeach

                {{-- Botón Siguiente --}}
                <button
                    wire:click="nextPage"
                    @disabled(!$p->hasMorePages())
                    class="relative inline-flex items-center {{ $border_btn_next_class[$border] ?? 'rounded-r-md' }} px-2 py-2
                           {{ $p->hasMorePages() ? 'text-gray-400 hover:bg-gray-50' : 'text-gray-300 pointer-events-none' }}">
                    <span class="sr-only">Siguiente</span>
                    <svg viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                              d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z"/>
                    </svg>
                </button>

            </nav>
        @endif
    </div>
</div>

