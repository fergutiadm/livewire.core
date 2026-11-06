{{-- resources/views/modules/ayuda/ayuda-articulo/form.blade.php --}}

<div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">

    <form
        novalidate
        wire:submit.prevent="save"
        x-on:submit="$dispatch('loading-start', { message: 'Guardando artículo de ayuda...' })"
        class="space-y-6"
    >

        {{-- ============================================================
             ENCABEZADO
             ============================================================ --}}
        <div class="flex flex-wrap items-start justify-between gap-4 border-b border-slate-200 pb-5">

            <div class="min-w-0">

                <h2 class="text-lg font-semibold text-slate-800">
                    {{ $ayudaArticuloId ? 'Editar artículo de ayuda' : 'Nuevo artículo de ayuda' }}
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Redacte el contenido que estará disponible en el centro de ayuda.
                </p>

            </div>

            {{-- Estado informativo: la publicación se gestiona por CQRS --}}
            @if($ayudaArticuloId)

                @if($publicado)
                    <span class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                        Publicado
                    </span>
                @else
                    <span class="inline-flex items-center gap-2 rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700">
                        <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                        Borrador
                    </span>
                @endif

            @else

                <span class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-medium text-slate-600">
                    <span class="h-2 w-2 rounded-full bg-slate-400"></span>
                    Nuevo borrador
                </span>

            @endif

        </div>

        {{-- ============================================================
             DATOS GENERALES
             ============================================================ --}}
        <div class="space-y-4">

            <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-600">
                Información general
            </h3>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">

                {{-- CATEGORÍA --}}
                <div>

                    <label
                        for="ayuda-categoria"
                        class="mb-1 block text-sm font-medium text-slate-700"
                    >
                        Categoría
                        <span class="text-rose-500">*</span>
                    </label>

                    <select
                        id="ayuda-categoria"
                        wire:model="categoriaId"
                        wire:change="categoriaChanged"
                        class="w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="">
                            Seleccione una categoría
                        </option>

                        @foreach($categorias as $categoria)
                            <option value="{{ $categoria->id }}">
                                {{ $categoria->nombre }}
                            </option>
                        @endforeach

                    </select>

                    @error('categoriaId')
                        <p class="panel-form-error mt-1 text-xs text-rose-600">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

                {{-- TÍTULO --}}
                <div>

                    <label
                        for="ayuda-titulo"
                        class="mb-1 block text-sm font-medium text-slate-700"
                    >
                        Título
                        <span class="text-rose-500">*</span>
                    </label>

                    <input
                        id="ayuda-titulo"
                        type="text"
                        wire:model="titulo"
                        maxlength="200"
                        class="w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="Ej.: Cómo registrar un producto"
                    >

                    @error('titulo')
                        <p class="panel-form-error mt-1 text-xs text-rose-600">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

                {{-- CONTEXTO --}}
                <div>

                    <label
                        for="ayuda-contexto"
                        class="mb-1 block text-sm font-medium text-slate-700"
                    >
                        Contexto
                    </label>

                    <input
                        id="ayuda-contexto"
                        type="text"
                        wire:model="contexto"
                        maxlength="160"
                        class="w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="Ej.: inventario.producto-variante"
                    >

                    <p class="mt-1 text-xs text-slate-500">
                        Identificador opcional para mostrar ayuda contextual.
                    </p>

                    @error('contexto')
                        <p class="panel-form-error mt-1 text-xs text-rose-600">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

            </div>

        </div>

        {{-- ============================================================
             RESUMEN
             ============================================================ --}}
        <div class="space-y-2">

            <label
                for="ayuda-resumen"
                class="block text-sm font-medium text-slate-700"
            >
                Resumen
            </label>

            <textarea
                id="ayuda-resumen"
                wire:model="resumen"
                rows="3"
                class="w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                placeholder="Breve descripción del contenido del artículo..."
            ></textarea>

            <p class="text-xs text-slate-500">
                Se utilizará como introducción en los listados y resultados de búsqueda.
            </p>

            @error('resumen')
                <p class="panel-form-error text-xs text-rose-600">
                    {{ $message }}
                </p>
            @enderror

        </div>

        {{-- ============================================================
             CONTENIDO — TIPTAP 3
             ============================================================ --}}
        <div class="space-y-3">

            <div class="flex flex-wrap items-center justify-between gap-2">

                <label class="block text-sm font-semibold text-slate-700">
                    Contenido del artículo
                </label>

                <span class="text-xs text-slate-400">
                    Editor de texto enriquecido
                </span>

            </div>

            <x-ui.tiptap
                model="contenido"
                placeholder="Redacte aquí el contenido de ayuda..."
            />

            <p class="text-xs text-slate-500">
                Puede utilizar títulos, negritas, listas y otros formatos para organizar las instrucciones.
            </p>

            @error('contenido')
                <p class="panel-form-error text-xs text-rose-600">
                    {{ $message }}
                </p>
            @enderror

        </div>

        {{-- ============================================================
        PIE DEL FORMULARIO
        ============================================================ --}}
        <div class="flex flex-col gap-4 border-t border-slate-200 pt-5 sm:flex-row sm:items-center sm:justify-between">

            <div class="text-xs text-slate-500">
                @if($ayudaArticuloId)
                    Los cambios del contenido se guardarán al confirmar.
                @else
                    El artículo se guardará inicialmente como borrador.
                @endif
            </div>

            <div class="flex flex-wrap items-center gap-3">

                {{-- CANCELAR --}}
                <button
                    type="button"
                    wire:click="cancel"
                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-300"
                >
                    <i class="bi bi-x-lg"></i>
                    Cancelar
                </button>

                {{-- GUARDAR --}}
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    wire:target="save"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60"
                >

                    <span wire:loading.remove wire:target="save">
                        <i class="bi bi-floppy"></i>
                        {{ $ayudaArticuloId ? 'Guardar cambios' : 'Crear artículo' }}
                    </span>

                    <span wire:loading wire:target="save">
                        <i class="bi bi-arrow-repeat"></i>
                        Guardando...
                    </span>

                </button>

            </div>

        </div>

    </form>

</div>
