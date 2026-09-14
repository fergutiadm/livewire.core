@props([


'sortable' => false,

'sortableMethod' => null,

'sortableOptions' => [],


])

<div class="bg-white border border-gray-200 rounded-xl shadow-sm">


<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200 text-sm">

        <thead class="bg-gray-50">
            {{ $head }}
        </thead>

        <tbody
            class="divide-y divide-gray-100 bg-white"
            @if($sortable)
                wire:sortable="{{ $sortableMethod }}"
                wire:sortable.options="{{ json_encode($sortableOptions) }}"
            @endif
        >
            {{ $slot }}
        </tbody>

    </table>
</div>


</div>
