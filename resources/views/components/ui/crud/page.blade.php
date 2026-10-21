{{-- resources/views/components/ui/crud/page.blade.php --}}
<div {{ $attributes->merge(['class' => 'space-y-6 w-full pt-6']) }}>

    @isset($header)
        <div class="w-full">
            {{ $header }}
        </div>
    @endisset

    @isset($head_container)
        {{ $head_container }}
    @endisset
    
    @isset($form)
        {{ $form }}
    @endisset
    
    @isset($table)
        {{ $table }}
    @endisset

    @isset($foot_container)
        {{ $foot_container }}
    @endisset

    @isset($deleteModal)
        {{ $deleteModal }}
    @endisset
</div>
