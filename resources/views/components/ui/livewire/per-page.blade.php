@props([
  'name' => 'perPage',
  'values' => [10,12,20,30,50,100],
  'current' => 12,
])

<div class="inline-flex items-center gap-2">
  <span class="text-sm text-gray-600">Por página</span>
  <select wire:model="{{ $name }}"
          class="rounded-lg border-gray-300 text-sm py-1.5 focus:ring-indigo-500 focus:border-indigo-500">
    @foreach($values as $n)
      <option value="{{ $n }}" @selected($current == $n)>{{ $n }}</option>
    @endforeach
  </select>
</div>
