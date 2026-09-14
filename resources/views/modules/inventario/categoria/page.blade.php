<div
    x-data="{ loading: false, loadingMessage: '', }"
    x-on:loading-start.window=" loading = true; loadingMessage = $event.detail.message ?? 'Cargando...'; "
    x-on:loading-stop.window=" loading = false; loadingMessage = ''; "
    x-on:media-procesada.window=" loading = false; loadingMessage = ''; "
    class="relative"
>
    <x-ui.crud.page>

        <x-slot:header>

            <x-ui.crud.header
                title="Categorías"
                description="Gestión de Categorías"
                :show-form="$showForm"
            />

        </x-slot:header>

        <x-slot:form>

            <x-ui.crud.form :show="$showForm">

                <livewire:inventario.categoria.form />

            </x-ui.crud.form>

        </x-slot:form>

        <x-slot:table>

            {{--  <livewire:inventario.categoria.table />  --}}
            {{-- TABLE --}}
            <livewire:inventario.categoria.table
                :local-id="$localId"
                {{--  lazy  --}}
                wire:key="categoria-table-{{ $tableVersion }}"
            />

        </x-slot:table>

        <x-slot:deleteModal>

            <x-ui.crud.delete-modal
                :show="$showDeleteModal"
            />

        </x-slot:deleteModal>

    </x-ui.crud.page>

    <x-ui.loading-overlay />
</div>
