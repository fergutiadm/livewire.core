<x-guest-layout>
    <div class="min-h-[80vh] grid place-items-center px-4">
        <div class="w-full max-w-md">
            <div class="text-center mb-6">
                <img src="{{ asset('img/ferguti-logo-1.png') }}" class="mx-auto h-12 w-12 rounded" alt="Logo">
                <h1 class="mt-3 text-xl font-semibold text-slate-900">Iniciar sesión</h1>
                <p class="text-sm text-slate-500">Accede al panel de administración</p>
            </div>
            <div class="rounded-2xl bg-white shadow-card ring-1 ring-slate-200 p-5 sm:p-6">
                <h1 class="text-xl font-semibold text-slate-900 mb-4">Crear cuenta</h1>

                <x-validation-errors class="mb-4" />

                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <input name="name" placeholder="Nombre" required class="mb-3 block w-full rounded-lg border-slate-300 px-3 py-2">
                    <input name="email" type="email" placeholder="Email" required class="mb-3 block w-full rounded-lg border-slate-300 px-3 py-2">
                    <input name="password" type="password" placeholder="Password" required class="mb-3 block w-full rounded-lg border-slate-300 px-3 py-2">
                    <input name="password_confirmation" type="password" placeholder="Confirmar password" required
                        class="mb-3 block w-full rounded-lg border-slate-300 px-3 py-2">

                    <button class="mt-2 w-full rounded-lg bg-indigo-600 py-2.5 text-white">
                        Registrarme
                    </button>
                </form>
            </div>

            <div class="mt-4 text-center text-sm">
                Ya tienes cuenta? <a href="{{ route('login') }}" class="text-slate-600 hover:text-slate-800">Inicia sesión</a>
            </div>
        </div>
    </div>
</x-guest-layout>
