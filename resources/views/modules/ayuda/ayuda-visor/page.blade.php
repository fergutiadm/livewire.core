{{-- resources/views/modules/ayuda/ayuda-visor/page.blade.php --}}
<div class="space-y-5">

    {{-- Encabezado --}}
    <header class="rounded-xl border border-slate-200
                   bg-gradient-to-br from-emerald-50
                   via-white to-indigo-50 p-5">

        <h1 class="text-xl font-bold text-slate-800">
            Centro de ayuda
        </h1>

        <p class="mt-1 text-sm text-slate-500">
            Documentación y asistencia del sistema.
        </p>

        <input
            type="search"
            wire:model.live.debounce.350ms="buscar"
            placeholder="Buscar en la ayuda..."
            class="mt-4 w-full rounded-lg border-slate-300
                   focus:border-emerald-500
                   focus:ring-emerald-500"
        />
    </header>

    <div class="grid grid-cols-1 gap-5 lg:grid-cols-4">

        {{-- Categorías --}}
        <aside class="space-y-2 lg:col-span-1">

            <h2 class="font-semibold text-slate-700">
                Categorías
            </h2>

            @foreach($categorias as $categoria)
                <div
                    wire:key="categoria-{{ $categoria->id }}"
                    class="rounded-lg border border-slate-200
                           bg-white px-3 py-2"
                >
                    <div class="text-sm font-medium text-slate-800">
                        {{ $categoria->nombre }}
                    </div>

                    <div class="text-xs text-slate-500">
                        {{ $categoria->articulos_publicados_count }}
                        artículos
                    </div>
                </div>
            @endforeach

        </aside>

        {{-- Artículos --}}
        <main class="space-y-3 lg:col-span-3">

            @if($articulo)

                <button
                    type="button"
                    wire:click="$set('articuloId', null)"
                    class="text-sm font-medium text-emerald-700"
                >
                    ← Volver a los artículos
                </button>

                <article class="rounded-xl border border-slate-200
                                bg-white p-5 shadow-sm">

                    <h2 class="text-xl font-bold text-slate-800">
                        {{ $articulo->titulo }}
                    </h2>

                    <div class="mt-4 prose prose-slate max-w-none">
                        {!! \Illuminate\Support\Str::markdown(
                            $articulo->contenido,
                            [
                                'html_input' => 'strip',
                                'allow_unsafe_links' => false,
                            ]
                        ) !!}
                    </div>

                </article>

            @else

                @forelse($articulos as $item)

                    <button
                        type="button"
                        wire:key="articulo-{{ $item->id }}"
                        wire:click="seleccionarArticulo({{ $item->id }})"
                        class="w-full rounded-xl border
                               border-slate-200 bg-white p-4
                               text-left shadow-sm transition
                               hover:border-emerald-300"
                    >
                        <h3 class="font-semibold text-slate-800">
                            {{ $item->titulo }}
                        </h3>

                        @if($item->resumen)
                            <p class="mt-1 text-sm text-slate-500">
                                {{ $item->resumen }}
                            </p>
                        @endif

                        <span class="mt-2 block text-xs text-emerald-700">
                            {{ $item->categoria->nombre }}
                        </span>
                    </button>

                @empty

                    <div class="rounded-xl border border-dashed
                                border-slate-300 p-8 text-center
                                text-sm text-slate-500">
                        No se encontraron artículos.
                    </div>

                @endforelse

            @endif

        </main>

    </div>
</div>
