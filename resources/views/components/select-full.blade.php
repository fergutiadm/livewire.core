@props([
    'options' => [],
    'placeholder' => null,
])

<select
    name="{{ $name }}"
    {{ $disabled ? 'disabled' : '' }}
    {{ $attributes->merge([
        'class' =>
            'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500
             rounded-md shadow-sm w-full'
    ]) }}
>
    @if($placeholder)
        <option value="">{{ $placeholder }}</option>
    @endif

    @foreach($options as $value => $label)
        <option value="{{ $value }}" @selected(old($name) == $value)>
            {{ $label }}
        </option>
    @endforeach
</select>

@error($name)
    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
@enderror