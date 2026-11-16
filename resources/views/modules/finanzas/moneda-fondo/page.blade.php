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
    <x-ui.crud.page layout="horizontal">

        <x-slot name="header">
            <x-ui.crud.header
                title="Movimientos de Fondo por Moneda"
                description="Registro de movimientos financieros del fondo"
                :show-form="$showForm"
                :show-toggle-on-mobile-only=true
                :selector-periodo=true
                :selector-fecha-operacion=true
                :mostrar-tasa-cambio="$mostrarTasaCambio"
            />
        </x-slot>

        <x-slot name="head_container">
            <div class="grid w-full grid-cols-1 md:grid-cols-[minmax(0,3fr)_minmax(320px,1fr)] gap-6">
        </x-slot>

        <x-slot name="form">
            <div class="min-w-0 md:order-2">
                <x-ui.crud.form :show="$showForm">

                    <livewire:finanzas.moneda-fondo.form />

                </x-ui.crud.form>
            </div>
        </x-slot>

        <x-slot:table>
            <div class="min-w-0 md:order-1">
                <livewire:finanzas.moneda-fondo.table
                    wire:key="moneda-fondo-table-{{ $tableVersion }}"
                />
            </div>

        </x-slot:table>

        <x-slot name="foot_container">
            </div>

            <livewire:ayuda.ayuda-contextual.panel
                contexto="finanzas.moneda_fondo"
                wire:key="ayuda-contextual-moneda-fondo"
            />
        </x-slot>

    </x-ui.crud.page>

    <x-ui.loading-overlay />
</div>