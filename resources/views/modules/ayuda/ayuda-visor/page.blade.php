<div class="min-h-screen space-y-6">

    {{-- Cabecera del Centro de ayuda --}}
    <header class="relative overflow-hidden rounded-2xl border border-slate-200 bg-gradient-to-br from-emerald-50 via-white to-indigo-50 px-6 py-8 sm:px-8">

        <div class="relative z-10 max-w-3xl">

            <div class="mb-3 inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-white/80 px-3 py-1 text-xs font-semibold text-emerald-700">
                <i class="bi bi-book-half"></i>
                DOCUMENTACIÓN DEL SISTEMA
            </div>

            <h1 class="text-2xl font-bold tracking-tight text-slate-800 sm:text-3xl">
                Centro de ayuda
            </h1>

            <p class="mt-2 max-w-2xl text-sm leading-relaxed text-slate-600 sm:text-base">
                Encuentra respuestas, consulta procedimientos y descubre cómo aprovechar las funcionalidades del sistema.
            </p>

            {{-- Buscador --}}
            <div class="relative mt-6 max-w-2xl">

                <i class="bi bi-search pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-lg text-slate-400"></i>

                <input
                    type="search"
                    wire:model.live.debounce.350ms="buscar"
                    placeholder="Buscar en la documentación..."
                    aria-label="Buscar artículos de ayuda"
                    class="w-full rounded-xl border border-slate-200 bg-white py-3.5 pl-12 pr-12 text-sm text-slate-700 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10"
                />

                @if(trim($buscar) !== '')
                    <button
                        type="button"
                        wire:click="$set('buscar', '')"
                        aria-label="Limpiar búsqueda"
                        class="absolute right-3 top-1/2 -translate-y-1/2 rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
                    >
                        <i class="bi bi-x-lg"></i>
                    </button>
                @endif

            </div>

            @if(trim($buscar) !== '')
                <p class="mt-2 text-xs text-slate-500">
                    Resultados para:
                    <span class="font-semibold text-slate-700">{{ $buscar }}</span>
                </p>
            @endif


        </div>

        <div class="pointer-events-none absolute -right-12 -top-16 hidden h-72 w-72 rounded-full border-[35px] border-emerald-100/50 lg:block"></div>
        <div class="pointer-events-none absolute -right-4 -top-8 hidden h-52 w-52 rounded-full border-[20px] border-indigo-100/40 lg:block"></div>

    </header>

    {{-- Cuerpo --}}
    <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-12">

        {{-- Navegación lateral --}}
        <aside class="lg:col-span-3">

            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">

                <div class="mb-4 flex items-center justify-between">

                    <h2 class="flex items-center gap-2 text-sm font-bold text-slate-800">
                        <i class="bi bi-folder2-open text-emerald-600"></i>
                        Categorías
                    </h2>

                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-500">
                        {{ $categorias->count() }}
                    </span>

                </div>

                <div class="flex gap-2 overflow-x-auto pb-2 lg:flex-col lg:overflow-visible lg:pb-0">

                    @forelse($categorias as $categoria)

                        <div
                            wire:key="categoria-{{ $categoria->id }}"
                            class="flex min-w-[220px] items-center justify-between gap-3 rounded-xl border border-transparent bg-slate-50 px-3 py-3 transition hover:border-emerald-100 hover:bg-emerald-50/60 lg:min-w-0"
                        >

                            <div class="flex min-w-0 items-center gap-3">

                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white text-emerald-600 shadow-sm">
                                    <i class="bi bi-folder2 text-lg"></i>
                                </div>

                                <span class="truncate text-sm font-medium text-slate-700">
                                    {{ $categoria->nombre }}
                                </span>

                            </div>

                            <span class="shrink-0 rounded-full bg-white px-2.5 py-1 text-xs font-semibold text-slate-500">
                                {{ $categoria->articulos_publicados_count }}
                            </span>

                        </div>

                    @empty

                        <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center xl:col-span-2">

                            @if(trim($buscar) !== '')

                                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-amber-50 text-3xl text-amber-500">
                                    <i class="bi bi-search"></i>
                                </div>

                                <h3 class="mt-4 font-semibold text-slate-700">
                                    No encontramos coincidencias
                                </h3>

                                <p class="mx-auto mt-2 max-w-md text-sm text-slate-500">
                                    No hay artículos que coincidan con "{{ $buscar }}".
                                    Prueba con otros términos.
                                </p>

                                <button
                                    type="button"
                                    wire:click="$set('buscar', '')"
                                    class="mt-5 inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700"
                                >
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                    Limpiar búsqueda
                                </button>

                            @else

                                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-50 text-3xl text-slate-300">
                                    <i class="bi bi-journal-x"></i>
                                </div>

                                <h3 class="mt-4 font-semibold text-slate-700">
                                    Todavía no hay documentación
                                </h3>

                                <p class="mx-auto mt-2 max-w-md text-sm text-slate-500">
                                    No hay artículos publicados disponibles en este momento.
                                </p>

                            @endif

                        </div>


                    @endforelse

                </div>

                <div class="mt-5 hidden border-t border-slate-100 pt-4 lg:block">

                    <div class="flex items-center gap-2 text-xs text-slate-400">
                        <i class="bi bi-info-circle"></i>
                        <span>Selecciona un artículo para consultar su contenido.</span>
                    </div>

                </div>

            </div>

        </aside>

        {{-- Contenido principal --}}
        <main class="min-w-0 lg:col-span-9">

            @if($articulo)

                {{-- Lectura del artículo --}}
                <div class="space-y-4">

                    <nav aria-label="Ruta de navegación" class="flex flex-wrap items-center gap-2 text-sm">

                        <button
                            type="button"
                            wire:click="volverAArticulos"
                            class="font-medium text-emerald-700 transition hover:text-emerald-900"
                        >
                            Centro de ayuda
                        </button>

                        <i class="bi bi-chevron-right text-xs text-slate-400"></i>

                        @if($articulo->categoria)
                            <span class="text-slate-500">
                                {{ $articulo->categoria->nombre }}
                            </span>

                            <i class="bi bi-chevron-right text-xs text-slate-400"></i>
                        @endif

                        <span class="font-medium text-slate-700">
                            {{ $articulo->titulo }}
                        </span>

                    </nav>


                    <article class="mx-auto max-w-5xl overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                        <div class="border-b border-slate-100 bg-slate-50/70 px-6 py-6 sm:px-8">

                            @if($articulo->categoria)

                                <div class="mb-3 inline-flex items-center gap-2 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                                    <i class="bi bi-folder2"></i>
                                    {{ $articulo->categoria->nombre }}
                                </div>

                            @endif

                            <h2 class="text-2xl font-bold leading-tight text-slate-800">
                                {{ $articulo->titulo }}
                            </h2>

                            @if($articulo->resumen)

                                <p class="mt-3 max-w-3xl text-sm leading-relaxed text-slate-600">
                                    {{ $articulo->resumen }}
                                </p>

                            @endif

                        </div>

                        <div class="px-6 py-7 sm:px-8 sm:py-9">

                            <div class="prose prose-slate max-w-none break-words
                                        prose-headings:font-bold
                                        prose-headings:tracking-tight
                                        prose-headings:text-slate-800
                                        prose-h2:mt-8 prose-h2:mb-4 prose-h2:text-xl
                                        prose-h3:mt-6 prose-h3:mb-3 prose-h3:text-lg
                                        prose-p:my-4 prose-p:leading-7
                                        prose-li:my-1 prose-li:leading-7
                                        prose-a:font-medium prose-a:text-emerald-700
                                        prose-strong:text-slate-800
                                        prose-blockquote:border-emerald-400
                                        prose-img:rounded-xl
                                        prose-hr:my-8">

                                {!! app(
                                    \App\Modules\Ayuda\Services\AyudaHtmlSanitizer::class
                                )->sanitize($articulo->contenido) !!}

                            </div>

                        </div>

                        <div class="border-t border-slate-100 bg-slate-50/50 px-6 py-4 sm:px-8">
                            <button
                                type="button"
                                wire:click="volverAArticulos"
                                class="inline-flex items-center gap-2 text-sm font-semibold text-emerald-700 transition hover:text-emerald-900"
                            >
                                <i class="bi bi-arrow-left"></i>
                                Regresar al listado
                            </button>
                        </div>

                    </article>

                </div>

            @else

                {{-- Listado de documentación --}}
                <div class="space-y-5">

                    <div class="flex flex-wrap items-center justify-between gap-3">

                        <div>
                            <h2 class="text-lg font-bold text-slate-800">
                                Explorar documentación
                            </h2>

                            <p class="mt-1 text-sm text-slate-500">
                                Consulta las guías y artículos disponibles.
                            </p>
                        </div>

                        <div class="inline-flex items-center gap-2 rounded-lg bg-white px-3 py-2 text-xs font-medium text-slate-500 ring-1 ring-slate-200">
                            <i class="bi bi-journal-text text-emerald-600"></i>
                            {{ $articulos->count() }}
                            {{ $articulos->count() === 1 ? 'artículo' : 'artículos' }}
                        </div>

                    </div>

                    <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">

                        @forelse($articulos as $item)

                            <button
                                type="button"
                                wire:key="articulo-{{ $item->id }}"
                                wire:click="seleccionarArticulo({{ $item->id }})"
                                class="group flex h-full flex-col rounded-2xl border border-slate-200 bg-white p-5 text-left shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-emerald-300 hover:shadow-md focus:outline-none focus:ring-4 focus:ring-emerald-500/10"
                            >

                                <div class="flex w-full items-start justify-between gap-3">

                                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-xl text-emerald-600 transition group-hover:bg-emerald-100">
                                        <i class="bi bi-file-earmark-text"></i>
                                    </div>

                                    <i class="bi bi-arrow-up-right text-lg text-slate-300 transition group-hover:text-emerald-600"></i>

                                </div>

                                <div class="mt-4 w-full flex-1">

                                    @if($item->categoria)

                                        <span class="text-xs font-semibold uppercase tracking-wide text-emerald-700">
                                            {{ $item->categoria->nombre }}
                                        </span>

                                    @endif

                                    <h3 class="mt-2 text-base font-bold text-slate-800 transition group-hover:text-emerald-700">
                                        {{ $item->titulo }}
                                    </h3>

                                    @if($item->resumen)

                                        <p class="mt-2 text-sm leading-relaxed text-slate-500">
                                            {{ $item->resumen }}
                                        </p>

                                    @else

                                        <p class="mt-2 text-sm text-slate-400">
                                            Consulta el contenido de este artículo.
                                        </p>

                                    @endif

                                </div>

                                <div class="mt-5 flex w-full items-center justify-between border-t border-slate-100 pt-4">

                                    <span class="text-xs font-medium text-slate-400">
                                        Consultar artículo
                                    </span>

                                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-700 transition group-hover:gap-2">
                                        Leer más
                                        <i class="bi bi-arrow-right"></i>
                                    </span>

                                </div>

                            </button>

                        @empty

                            <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center xl:col-span-2">

                                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-50 text-3xl text-slate-300">
                                    <i class="bi bi-search"></i>
                                </div>

                                <h3 class="mt-4 font-semibold text-slate-700">
                                    No se encontraron artículos
                                </h3>

                                <p class="mx-auto mt-2 max-w-md text-sm text-slate-500">
                                    No hay documentación que coincida con tu búsqueda. Prueba con otro término.
                                </p>

                            </div>

                        @endforelse

                    </div>

                </div>

            @endif

        </main>

    </div>

</div>
