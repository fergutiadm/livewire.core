<x-guest-layout>
    <div class="min-h-[80vh] grid place-items-center px-4">
        <div class="w-full max-w-md">
            <div class="text-center mb-6">
                <img src="{{ asset('img/ferguti-logo-1.png') }}" class="mx-auto h-12 w-12 rounded" alt="Logo">
                <h1 class="mt-3 text-xl font-semibold text-slate-900">Iniciar sesión</h1>
            </div>
            <div class="text-center mb-6">
                <img src="{{ asset('img/ferguti-logo-1.png') }}" class="mx-auto h-12 w-12 rounded" alt="Logo">
                <h1 class="mt-3 text-xl font-semibold text-slate-900">Iniciar sesión</h1>
            </div>
            <div class="rounded-2xl bg-white shadow-card ring-1 ring-slate-200 p-5 sm:p-6">
                <x-validation-errors class="mb-4" />
                {{ $slot ?? '' }}
            </div>
        </div>
    </div>
</x-guest-layout>
