@php
    $seccion = 'Construccion';
@endphp
<x-app-layout_admin>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __($seccion) }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6 dark:text-slate-800">
                Sección en Contrucción
            </div>
        </div>
    </div>
</x-app-layout_admin>
