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
    class="relative"
>
    <x-ui.crud.page>

        <x-slot name="header">
            <x-ui.crud.header
                title="Periodos Contables"
                description="Gestión de periodos contables"
                :show-form="$showForm"
            />
        </x-slot>

        <x-slot name="form">
            <x-ui.crud.form :show="$showForm">

                <livewire:contabilidad.periodo-contable.form />

            </x-ui.crud.form>
        </x-slot>

        <x-slot:table>

            <livewire:contabilidad.periodo-contable.table
                :local-id="$localId"
                {{--  lazy  --}}
                wire:key="periodo_contable-table-{{ $tableVersion }}"
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