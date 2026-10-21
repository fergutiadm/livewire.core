@php

@endphp

<div>
    <button
        type="button"
        wire:click="openModal"
        class="flex w-full items-center justify-between gap-2 rounded-xl border border-emerald-200 bg-emerald-50/60 px-3 py-2 text-xs md:text-sm font-medium text-slate-700 shadow-sm hover:bg-emerald-50 transition-colors"
    >
        <div class="flex items-center gap-1.5 min-w-0">
            <span class="text-emerald-600/80 font-normal">Gestionar cierre diario:</span>
        </div>
        <svg class="h-4 w-4 flex-shrink-0 text-emerald-600/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"/>
        </svg>
    </button>

    @if ($showModal)
        <div
            class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/40 px-4"
            wire:click.self="closeModal"
        >
            <div
                class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl"
            >
                <div class="mb-5 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-slate-800">
                        Cierre diario
                    </h2>

                    <button
                        type="button"
                        wire:click="closeModal"
                        class="text-slate-400 hover:text-slate-600"
                    >
                        ✕
                    </button>
                </div>

                <div class="space-y-2">

                    <span class="font-bold mx-3 p-3">
                        Modal Cierre Diario
                    </span>
                    <livewire:finanzas.cierre-caja.page 
                        wire:key="cierre-caja-page"
                    />
                </div>
            </div>
        </div>
    @endif
</div>