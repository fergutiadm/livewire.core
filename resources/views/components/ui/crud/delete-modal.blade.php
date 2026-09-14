@props([
    'show' => false,
    'title' => 'Confirmar eliminación',
    'message' => '¿Está seguro de que desea eliminar este registro?',
    'cancelMethod' => 'cancelarEliminacion',
    'confirmMethod' => 'confirmarEliminacion',
    'cancelText' => 'Cancelar',
    'confirmText' => 'Eliminar',
])

@if($show)

    <div
        class="fixed inset-0 z-50 flex items-center justify-center"
        wire:key="crud-delete-modal"
    >

        {{-- BACKDROP --}}
        <div
            class="absolute inset-0 bg-black/50"
            wire:click="{{ $cancelMethod }}"
        ></div>

        {{-- MODAL --}}
        <div
            class="
                relative
                w-full
                max-w-md
                rounded-xl
                bg-white
                p-6
                shadow-xl
            "
        >

            <h2 class="text-lg font-semibold text-slate-800">
                {{ $title }}
            </h2>

            <p class="mt-2 text-sm text-gray-600">
                {{ $message }}
            </p>

            <div class="mt-6 flex justify-end gap-3">

                <button
                    type="button"
                    wire:click="{{ $cancelMethod }}"
                    class="
                        rounded-lg
                        bg-gray-200
                        px-4
                        py-2
                        text-sm
                        text-gray-700
                        hover:bg-gray-300
                    "
                >
                    {{ $cancelText }}
                </button>

                <button
                    type="button"
                    wire:click="{{ $confirmMethod }}"
                    x-on="$dispatch('loading-start', { message: 'Eliminando...' })"
                    class="
                        rounded-lg
                        bg-red-600
                        px-4
                        py-2
                        text-sm
                        text-white
                        hover:bg-red-700
                    "
                >
                    {{ $confirmText }}
                </button>

            </div>

        </div>

    </div>

@endif