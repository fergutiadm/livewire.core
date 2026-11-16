{{-- resources/views/modules/finanzas/banco-sucursal/page.blade.php --}}
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
            <x-ui.crud.header
                title="Sucursales"
                description="Gestión de sucursales"
                :show-form="$showForm"
            />
        </x-slot>

        <x-slot name="form">
            <x-ui.crud.form :show="$showForm">

                <livewire:finanzas.banco-sucursal.form />

            </x-ui.crud.form>
        </x-slot>

        <x-slot:table>

            <livewire:finanzas.banco-sucursal.table
                {{--  lazy  --}}
                wire:key="banco-sucursal-table-{{ $tableVersion }}"
            />

        </x-slot:table>

        <x-slot name="deleteModal">
            <x-ui.crud.delete-modal
                :show="$showDeleteModal"
            />
        </x-slot>

    </x-ui.crud.page>

    <x-ui.loading-overlay />
</div>