@if($rows->hasPages())

    <div class="mt-4">

        <x-ui.livewire.pagination
            :paginator="$rows"
            :window="2"
            :show-summary="true"
        >

            <x-slot:middle>

                <x-ui.livewire.per-page
                    wire:model.live="perPage"
                    class="hidden sm:inline-flex"
                />

            </x-slot:middle>

        </x-ui.livewire.pagination>

    </div>

@endif