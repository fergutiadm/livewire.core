<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6">
                @livewire('select-producto', ['localId'=>1])
            </div>
            <div class="bg-white shadow rounded-lg p-6 mt-4">
                <div class="panel-layout-3 bg-white rounded-lg shadow-lg w-full max-w-none">
                    {{-- PANEL HEADER --}}
                    <div class="panel-header m-2 item">
                        <h3 class="text-lg font-semibold">
                            Editando Sub Categoría – Categoría CAT1
                        </h3>
                    </div>
                    {{-- PANEL IZQUIERDO --}}
                    <div class="panel-left m-2 bg-white-1 overflow-x-auto">
                        <div class="p-4">
                            <h4 class="font-semibold mb-3">Atributos seleccionados</h4>
                            <p class="text-sm text-gray-500 col-span-full">
                                No hay atributos seleccionados
                            </p>
                            <div class="mt-4 panel-footer-atributo">
                                <x-button
                                    class="btn-primary-1">
                                    Guardar Sub Categorías
                                </x-button>
                            </div>
                            <div class="panel-footer-atributo border-none">
                                <label class="flex items-center gap-2 text-sm">
                                    <x-checkbox type="checkbox"/>
                                    <span>Propagar estos valores a todos los productos de la categoría</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    {{-- PANEL DERECHO --}}
                    <div class="panel-right m-2 panel-form">
                        formulario
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
