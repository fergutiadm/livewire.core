<button
    wire:click="$dispatch('{{ $event }}', { id: {{ $row->id }} })"
    @if($loadingMessage)
        x-on:click="$dispatch('loading-start', { message: @js($loadingMessage) })"
    @endif
    class="inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-xs font-medium text-white transition {{ $class }}"
>
    @if($icon)
        <x-dynamic-component :component="$icon" class="w-4 h-4" />
    @endif

    {{ $label }}
</button>