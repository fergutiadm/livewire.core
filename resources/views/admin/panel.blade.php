<x-app-layout_admin>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6 mb-4">
                Panel de Administración
            </div>
            <div class="bg-white shadow rounded-lg p-6 mb-4">
                @livewire('select-atributo-producto', ['localId'=>1])
            </div>
            <div class="bg-white shadow rounded-lg p-6">
                @livewire('select-producto', ['localId'=>1])
            </div>
        </div>
    </div>
</x-app-layout_admin>
