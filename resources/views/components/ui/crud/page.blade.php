<div {{ $attributes->merge(['class' => 'space-y-6']) }}>

    @isset($header)
        {{ $header }}
    @endisset

    @isset($form)
        {{ $form }}
    @endisset

    @isset($table)
        {{ $table }}
    @endisset

    @isset($deleteModal)
        {{ $deleteModal }}
    @endisset
</div>
