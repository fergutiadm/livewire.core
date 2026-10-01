<x-ui.crud.page>

    <x-slot name="header">
        <x-ui.crud.header
            title="__ENTITY__"
            description="Gestión de __ENTITY_LOWER__"
            :show-form="$showForm"
        />
    </x-slot>

    <x-slot name="form">
        <livewire:__MODULE_LOWER__.__ENTITY_LOWER__.form />
    </x-slot>

    <livewire:__MODULE_LOWER__.__ENTITY_LOWER__.table />

    <x-slot name="deleteModal">
        <x-ui.crud.delete-modal
            :show="$showDeleteModal"
        />
    </x-slot>

</x-ui.crud.page>