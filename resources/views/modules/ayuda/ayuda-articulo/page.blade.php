<div
    x-data="{ loading: false, loadingMessage: '', }"
    x-on:loading-start.window=" loading = true; loadingMessage = $event.detail.message ?? 'Cargando...'; "
    x-on:loading-stop.window=" loading = false; loadingMessage = ''; "
    x-on:media-procesada.window=" loading = false; loadingMessage = ''; "

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

        <x-slot:header>

            <x-ui.crud.header
                title="Artículos de Ayuda"
                description="Gestión de Artículos de Ayuda"
                :show-form="$showForm"
            />

        </x-slot:header>

        <x-slot:form>

            <x-ui.crud.form :show="$showForm">

                <livewire:ayuda.ayuda-articulo.form />

            </x-ui.crud.form>

        </x-slot:form>

        <x-slot:table>

            {{-- TABLE --}}
            <livewire:ayuda.ayuda-articulo.table
                {{--  lazy  --}}
                wire:key="ayuda-articulo-table-{{ $tableVersion }}"
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
