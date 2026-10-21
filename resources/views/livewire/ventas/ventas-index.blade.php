{{-- resources/views/livewire/ventas/ventas-index.blade.php --}}
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

    <x-ui.crud.page class="pt-6" layout="horizontal">

        <x-slot name="header">
            <x-ui.crud.header
                title="Ventas"
                description="Gestión de ventas"
                :show-form="$showForm"
                :show-toggle=false
                :selector-periodo=true
                :selector-fecha-operacion=true
                :gestion-cierre=true
            />
        </x-slot>

        
        <x-slot name="head_container">
            <div class="grid w-full grid-cols-1 md:grid-cols-[minmax(0,3fr)_minmax(320px,1fr)] gap-6">
        </x-slot>

        <x-slot name="form">
            <div class="min-w-0 md:order-2">
                <x-ui.crud.form :show="$showForm">

                    <span class="font-semibold text-slate-500">Formulario</span>
                    {{--
                    <livewire:ventas.venta.form 
                        wire:key="venta-form"
                    />
                    --}}

                </x-ui.crud.form>
            </div>
        </x-slot>

        <x-slot:table>
            <div class="min-w-0 md:order-1">
                <span class="font-semibold text-slate-500">Tabla</span>
                {{--
                <livewire:ventas.venta.table
                    wire:key="venta-table-{{ $tableVersion }}"
                />
                --}}
            </div>

        </x-slot:table>

        <x-slot name="foot_container">
            </div>
        </x-slot>

        <x-slot name="deleteModal">
            <x-ui.crud.delete-modal
                :show="$showDeleteModal"
            />
        </x-slot>

    </x-ui.crud.page>

    <x-ui.loading-overlay />
</div>