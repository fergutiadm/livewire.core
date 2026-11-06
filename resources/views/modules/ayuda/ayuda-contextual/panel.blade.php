<div>
    {{-- Botón flotante --}}
    <button
        type="button"
        wire:click="abrir"
        class="fixed bottom-3 right-3 z-40 inline-flex items-center
        {{-- class="fixed bottom-5 right-5 z-40 inline-flex items-center --}}
               gap-2 rounded-full bg-indigo-500 px-2 py-0.5
               text-xs font-semibold text-white shadow-lg
               transition hover:bg-indigo-600"
        aria-label="Abrir ayuda contextual"
    >
        <i class="bi bi-question-circle text-lg"></i>
        {{-- Ayuda --}}
    </button>

    @if($abierto)

        <div class="fixed inset-0 z-50">

            {{-- Fondo --}}
            <button
                type="button"
                wire:click="cerrar"
                class="absolute inset-0 h-full w-full bg-slate-900/40"
                aria-label="Cerrar ayuda"
            ></button>

            {{-- Panel lateral --}}
            <aside
                class="absolute inset-y-0 right-0 flex w-full
                       flex-col bg-white shadow-2xl
                       sm:max-w-md"
            >

                {{-- Encabezado --}}
                <header class="flex items-center justify-between
                               border-b border-slate-200
                               bg-gradient-to-r from-emerald-50
                               to-indigo-50 p-5">

                    <div>
                        <h2 class="font-bold text-slate-800">
                            Ayuda contextual
                        </h2>

                        <p class="mt-1 text-xs text-slate-500">
                            Información relacionada con esta sección.
                        </p>
                    </div>

                    <button
                        type="button"
                        wire:click="cerrar"
                        class="rounded-lg p-2 text-slate-500
                               hover:bg-white hover:text-slate-800"
                        aria-label="Cerrar"
                    >
                        <i class="bi bi-x-lg"></i>
                    </button>

                </header>

                {{-- Contenido --}}
                <div class="flex-1 overflow-y-auto p-5">

                    @if($articulo)

                        <button
                            type="button"
                            wire:click="volverListado"
                            class="mb-4 text-sm font-medium text-indigo-600"
                        >
                            ← Volver a los artículos
                        </button>

                        <h3 class="text-lg font-bold text-slate-800">
                            {{ $articulo->titulo }}
                        </h3>

                        <div class="mt-4 prose prose-slate max-w-none">
                            {!! app(\App\Modules\Ayuda\Services\AyudaHtmlSanitizer::class)
                                ->sanitize($articulo->contenido) !!}
                        </div>

                    @else

                        <h3 class="mb-4 text-sm font-semibold
                                   uppercase tracking-wide text-slate-500">
                            Artículos disponibles
                        </h3>

                        @forelse($articulos as $item)

                            <button
                                type="button"
                                wire:key="ayuda-contextual-{{ $item->id }}"
                                wire:click="seleccionarArticulo({{ $item->id }})"
                                class="mb-3 w-full rounded-xl border
                                       border-slate-200 bg-white p-4
                                       text-left transition
                                       hover:border-indigo-300
                                       hover:bg-indigo-50/30"
                            >
                                <h4 class="font-semibold text-slate-800">
                                    {{ $item->titulo }}
                                </h4>

                                @if($item->resumen)
                                    <p class="mt-1 text-sm text-slate-500">
                                        {{ $item->resumen }}
                                    </p>
                                @endif

                            </button>

                        @empty

                            <div class="rounded-xl border border-dashed
                                        border-slate-300 p-6 text-center">
                                <i class="bi bi-info-circle text-2xl
                                          text-slate-400"></i>

                                <p class="mt-2 text-sm text-slate-500">
                                    No hay artículos de ayuda para esta sección.
                                </p>
                            </div>

                        @endforelse

                    @endif

                </div>

                {{-- Pie --}}
                <footer class="border-t border-slate-200 p-3">
                    <p class="text-center text-xs text-slate-400">
                        Centro de ayuda del sistema
                    </p>
                </footer>

            </aside>
        </div>

    @endif
</div>