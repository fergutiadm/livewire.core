{{-- resources/views/modules/configuracion/setup-config/page.blade.php --}}
<div
    x-data="{ loading: false, loadingMessage: '' }"
    x-on:loading-start.window="
        loading = true;
        loadingMessage = $event.detail.message ?? 'Cargando...';
    "
    x-on:loading-stop.window="
        loading = false;
        loadingMessage = '';
    "
    x-on:media-procesada.window="
        loading = false;
        loadingMessage = '';
    "

    x-init="
        Livewire.hook('request', ({ fail }) => {
            fail(() => {
                loading = false;
                loadingMessage = '';
            });
        });

        Livewire.hook('commit', ({ succeed }) => {
            succeed(() => {
                loading = false;
                loadingMessage = '';
            });
        });
    "
    class="relative"
>
    
    <x-ui.crud.page>
        <x-slot name="header">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h1 class="text-xl font-semibold text-gray-800">
                        Configuración del sistema
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Administra las variables globales de configuración.
                    </p>
                </div>

                <button
                    type="button"
                    wire:click="toggleForm"
                    class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
                >
                    {{ $showForm ? 'Ocultar formulario' : 'Nueva configuración' }}
                </button>
            </div>
        </x-slot>

        <x-slot name="form">
            @if ($showForm)
                <livewire:configuracion.setup-config.form
                    :key="'setup-config-form'"
                />
            @endif
        </x-slot>

        <x-slot name="table">
            <livewire:configuracion.setup-config.table
                :key="'setup-config-table-'.$tableVersion"
            />
        </x-slot>

        <x-slot name="deleteModal">
            @if ($showDeleteModal)
                <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 p-4">
                    <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
                        <h2 class="text-lg font-semibold text-gray-900">
                            Eliminar configuración
                        </h2>

                        <p class="mt-2 text-sm text-gray-600">
                            ¿Confirmas que deseas eliminar esta configuración?
                        </p>

                        <div class="mt-6 flex justify-end gap-3">
                            <button
                                type="button"
                                wire:click="cancelarEliminacion"
                                class="rounded-lg border border-gray-300 px-4 py-2 text-sm"
                            >
                                Cancelar
                            </button>

                            <button
                                type="button"
                                wire:click="confirmarEliminacion"
                                class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700"
                            >
                                Eliminar
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </x-slot>
    </x-ui.crud.page>

    <x-ui.loading-overlay />
</div>