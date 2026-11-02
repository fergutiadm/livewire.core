{{-- resources/views/livewire/currency/rates-bar.blade.php --}}
<div>
    @if($items->isNotEmpty())
        <div
            class="w-full rounded-2xl border shadow-lg
            shadow-black bg-white bg-opacity-10 px-3
            py-2 text-xs sm:text-sm text-slate-700"
        >

            {{-- Variante stacked --}}
            @if($variant === 'stacked')
                <div class="flex flex-col gap-2">
                    {{-- Moneda principal --}}
                    <div class="inline-flex items-center gap-2">
                        <span class="cursor-pointer inline-flex items-center gap-1 rounded-full px-3 py-1
                            {{ $primary['color_bg'] ?? 'bg-slate-900' }} {{ $primary['color_text'] ?? 'text-white' }}"
                            wire:click="openModal"
                            >
                            <span class="font-semibold">{{ $primary['codigo'] ?? '—' }}</span>
                            <span class="text-xs opacity-80">base</span>
                        </span>
                        <span class="text-slate-400">•</span>
                    </div>

                    {{-- Otras monedas --}}
                    @foreach($others as $it)
                        <div class="rounded-xl bg-slate-100 bg-opacity-45 px-3 py-2">
                            <div class="flex flex-col gap-1">
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs
                                        {{ $it['color_bg'] ?? 'bg-white' }} {{ $it['color_text'] ?? 'text-slate-800' }}">
                                        <span class="font-semibold">{{ $it['codigo'] }}</span>
                                    </span>

                                    <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[11px]
                                        {{ $it['color_bg'] ?? 'bg-white' }} {{ $it['color_text'] ?? 'text-slate-800' }}">
                                        @if(!$shortView)
                                        <span>1 {{ $it['codigo'] ?? '—' }}</span>
                                        <span>=</span>
                                        @endif
                                        <span class="font-mono">{{ number_format($it['eq_per_primary'], $decimals) }}</span>
                                        @if(!$shortView)
                                        <span>{{ $primary['codigo'] }}</span>
                                        @endif
                                    </span>
                                </div>

                                @if($showBoth)
                                    <div class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[11px]
                                        {{ $it['color_bg'] ?? 'bg-white' }} {{ $it['color_text'] ?? 'text-slate-800' }}">
                                        <span>1 {{ $primary['codigo'] }}</span>
                                        <span>=</span>
                                        <span class="font-mono">{{ number_format($it['inverse'], $decimals) }}</span>
                                        <span>{{ $it['codigo'] ?? '—' }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

            @else
                {{-- Variante horizontal --}}
                <div class="flex gap-2 items-center justify-center whitespace-nowrap overflow-x-auto overscroll-x-contain snap-x scroll-px-2 pr-2">
                    <span class="cursor-pointer shrink-0 snap-start inline-flex items-center gap-1 rounded-full px-3 py-1
                            {{ $primary['color_bg'] ?? 'bg-slate-900' }} {{ $primary['color_text'] ?? 'text-white' }}"
                            wire:click="openModal"
                            >
                        <span class="font-semibold">{{ $primary['codigo'] ?? '—' }}</span>
                        <span class="text-xs opacity-80">base</span>
                    </span>
                    <span class=" text-slate-400 shrink-0">•</span>

                    @foreach($others as $it)
                        {{-- <span class="shrink-0 snap-start inline-flex items-center gap-1 sm:gap-2 rounded-full bg-slate-400 px-3 py-1"> --}}
                            <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs
                                    {{ $it['color_bg'] ?? 'bg-white' }} {{ $it['color_text'] ?? 'text-slate-800' }}">
                                <span class="font-semibold">{{ $it['codigo'] }}</span>
                            {{-- </span>
                            <span class="inline-flex items-center gap-1 rounded-full px-1 sm:px-2 py-0.5 text-xs"> --}}
                                @if(!$shortView)
                                <span>1 {{ $it['codigo'] ?? '—' }}</span>
                                <span>=</span>
                                @endif
                                <span class="font-mono">{{ number_format($it['eq_per_primary'], $decimals) }}</span>
                                @if(!$shortView)
                                <span>{{ $primary['codigo'] }}</span>
                                @endif
                            </span>

                            @if($showBoth)
                                <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs">
                                    <span>1 {{ $primary['codigo'] }}</span>
                                    <span>=</span>
                                    <span class="font-mono">{{ number_format($it['inverse'], $decimals) }}</span>
                                    <span>{{ $it['codigo'] ?? '—' }}</span>
                                </span>
                            @endif
                        {{-- </span> --}}
                    @endforeach
                </div>
            @endif
        </div>

        @if($showModal)
        @stack('modals')
            <div
                class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/40 px-4"
                wire:click.self="closeModal"
                @keydown.escape.window="$wire.closeModal()"
            >
                {{-- Contenedor principal --}}
                <section
                    role="dialog"
                    aria-modal="true"
                    aria-labelledby="tasas-cambio-modal-title"
                    class="flex max-h-[calc(100dvh-1rem)] w-full max-w-md
                        flex-col overflow-hidden rounded-2xl
                        border border-white/70 bg-white
                        shadow-2xl shadow-slate-950/20
                        sm:max-h-[90vh] sm:rounded-3xl"
                >
                    {{-- Encabezado --}}
                    <header
                        class="relative shrink-0 overflow-hidden border-b
                            border-slate-200 bg-gradient-to-br
                            from-fuchsia-50 via-white to-indigo-50
                            px-4 py-4 sm:px-6 sm:py-5"
                    >
                        <div
                            class="pointer-events-none absolute -right-8 -top-12
                                h-36 w-36 rounded-full
                                bg-fuchsia-100/60 blur-2xl"
                        ></div>

                        <div class="relative flex items-start gap-3">

                            <div
                                class="flex h-11 w-11 shrink-0 items-center
                                    justify-center rounded-xl
                                    bg-fuchsia-100 text-fuchsia-700
                                    ring-1 ring-fuchsia-200/70"
                            >
                                <i class="bi bi-currency-exchange text-xl"></i>
                            </div>

                            <div class="min-w-0 flex-1">
                                <h2
                                    id="tasas-cambio-modal-title"
                                    class="text-lg font-bold text-slate-900"
                                >
                                    Tasas de cambio
                                </h2>

                                <p class="mt-1 text-xs leading-5 text-slate-600">
                                    Actualiza la equivalencia de las monedas
                                    respecto a la moneda principal.
                                </p>
                            </div>

                            <button
                                type="button"
                                wire:click="closeModal"
                                aria-label="Cerrar"
                                class="flex h-10 w-10 shrink-0 items-center
                                    justify-center rounded-xl
                                    border border-slate-200 bg-white/90
                                    text-slate-500 transition
                                    hover:bg-white hover:text-slate-900"
                            >
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                    </header>
                    <div class="mt-4 space-y-2">
                        <form novalidate
                            wire:submit.prevent="guardarTasasCambio"
                            x-on:submit="$dispatch('loading-start', { message: 'Guardando Tasas de Cambio...' })"
                            class="space-y-6"
                        >
                            {{-- Otras monedas --}}
                            @foreach($others as $otherId => $it)
                                <div class="flex flex-col items-center justify-between gap-2 border-b border-slate-100 pb-4 px-3">
                                    <div class="flex flex-row items-center justify-between gap-4">
                                        <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs
                                            {{ $it['color_bg'] ?? 'bg-white' }} {{ $it['color_text'] ?? 'text-slate-800' }}">
                                            <span class="font-semibold">{{ $it['codigo'] }}</span>
                                        </span>
                                        <input
                                            type="text"
                                            wire:model.blur="others.{{ $otherId }}.tasa_cambio"
                                            wire.blur="actualizarTasasCambio({{ $otherId }})"
                                            @class([
                                                'w-full rounded-xl border px-3 py-2 text-right text-sm',
                                                'border-red-400 focus:border-red-500 focus:ring-red-500'
                                                    => $errors->has("others.$otherId.tasa_cambio"),
                                                'border-slate-300 focus:border-indigo-500 focus:ring-indigo-500'
                                                    => !$errors->has("others.$otherId.tasa_cambio"),
                                            ])
                                        >
                                    </div>
                                    <div>
                                        @error("others.$otherId.tasa_cambio")<p class="panel-form-error">{{ $message }}</p>@enderror
                                    </div>
                                </div>
                            @endforeach

                            {{-- ACTIONS --}}
                            <div class="flex items-center justify-center gap-3 pb-3">

                                <button
                                    type="submit"
                                    @disabled(!$this->hayCambios())
                                    @class([
                                        'btn-primary',
                                        'opacity-50 cursor-not-allowed' => !$this->hayCambios(),
                                    ])
                                    class="
                                        inline-flex
                                        items-center
                                        rounded-xl
                                        bg-indigo-600
                                        px-5
                                        py-2
                                        text-sm
                                        font-medium
                                        text-white
                                        hover:bg-indigo-700
                                    "
                                >

                                    Guardar

                                </button>

                                <button
                                    type="button"
                                    wire:click="closeModal"
                                    class="
                                        inline-flex
                                        items-center
                                        rounded-xl
                                        border
                                        border-slate-300
                                        bg-white
                                        px-5
                                        py-2
                                        text-sm
                                        font-medium
                                        text-slate-700
                                        hover:bg-slate-50
                                    "
                                >

                                    Cancelar

                                </button>

                            </div>
                        </form>
                    </div>
                </section>
            </div>
        @endstack
        @endif
    @endif
</div>
