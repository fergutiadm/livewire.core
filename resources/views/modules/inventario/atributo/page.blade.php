{{-- resources/views/modules/inventario/atributo/page.blade.php --}}
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
                title="Atributos"
                description="Gestión de atributos y sus valores"
                :show-form="$showForm"
            />
        </x-slot>

        <x-slot name="form">
            <x-ui.crud.form :show="$showForm">
                <livewire:inventario.atributo.form />
            </x-ui.crud.form>
        </x-slot>

        <x-slot name="table">
            <livewire:inventario.atributo.table
                wire:key="atributo-table-{{ $tableVersion }}"
            />
        </x-slot>

        <x-slot name="deleteModal">
            <x-ui.crud.delete-modal
                :show="$showDeleteModal"
                title="Desactivar atributo"
                message="¿Está seguro de que desea desactivar este atributo?"
                confirm-text="Desactivar"
                loading-message="Desactivando atributo..."
            />
        </x-slot>

    </x-ui.crud.page>

    <x-ui.loading-overlay />
</div>