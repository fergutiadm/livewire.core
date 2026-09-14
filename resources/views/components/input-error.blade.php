@props(['for'])

@error($for)
    <p {{ $attributes->merge(['class' => 'text-sm text-red-600 panel-form-error']) }}>{{ $message }}</p>
@enderror
