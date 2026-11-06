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
    "
    class="relative"
>
    <x-ui.crud.page>

        <x-slot name="header">
            <x-ui.crud.header
                title="Unidades de Medida"
                description="Gestión de unidades de medida"
                :show-form="$showForm"
            />
        </x-slot>

        <x-slot name="form">
            <x-ui.crud.form :show="$showForm">

                <livewire:inventario.unidad-medida.form
                    :unidad-dimension-id="$unidadDimensionId"
                    :key="'unidad-medida-form'" 
                />

            </x-ui.crud.form>
        </x-slot>

        <x-slot:table>

            <livewire:inventario.unidad-medida.table
                {{--  lazy  --}}
                :unidad-dimension-id="$unidadDimensionId"
                wire:key="unidad-medida-table-{{ $tableVersion }}"
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