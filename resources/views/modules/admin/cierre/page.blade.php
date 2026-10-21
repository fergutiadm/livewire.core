<div
    x-data="{ loading: false, loadingMessage: '' }"
    x-on:loading-start.window="loading = true; loadingMessage = $event.detail.message ?? 'Cargando...';"
    x-on:loading-stop.window="loading = false; loadingMessage = '';"
    class="relative"
>
    <x-ui.crud.page>
        <x-slot name="header">
            <x-ui.crud.header title="Cierre Diario de Caja" description="Arqueo físico de billetes y validación de saldo contable" :show-form="$showForm" :show-toggle="false" />
        </x-slot>
        <x-slot name="form">
            <x-ui.crud.form :show="$showForm">
                <livewire:admin.cierre.form />
            </x-ui.crud.form>
        </x-slot>
    </x-ui.crud.page>
    <x-ui.loading-overlay />
</div>