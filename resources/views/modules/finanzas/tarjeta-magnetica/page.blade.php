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
                title="Tarjetas Magnéticas"
                description="Gestión de tarjetas magnéticas"
                :show-form="$showForm"
            />
        </x-slot>

        <x-slot name="form">
            <x-ui.crud.form :show="$showForm">

                <livewire:finanzas.tarjeta-magnetica.form />

            </x-ui.crud.form>
        </x-slot>

        <x-slot:table>

            <livewire:finanzas.tarjeta-magnetica.table
                {{--  lazy  --}}
                wire:key="tarjeta_magnetica -table-{{ $tableVersion }}"
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