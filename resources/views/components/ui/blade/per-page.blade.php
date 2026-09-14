@props([
  'name' => 'per_page',
  'values' => [10,12,20,30,50,100],
  'current' => (int) request('per_page', 12),
])

<form method="GET" {{ $attributes->merge(['class'=>'inline-flex items-center gap-2']) }}>
  @foreach(request()->except($name,'page') as $k => $v)
    <input type="hidden" name="{{ $k }}" value="{{ $v }}">
  @endforeach
  <span class="text-sm text-gray-600">Por página</span>
  <select name="{{ $name }}" onchange="this.form.submit()"
          class="rounded-lg border-gray-300 text-sm py-1.5 focus:ring-indigo-500 focus:border-indigo-500">
    @foreach($values as $n)
      <option value="{{ $n }}" @selected((int)$current === (int)$n)>{{ $n }}</option>
    @endforeach
  </select>
</form>
