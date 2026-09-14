<x-guest-layout>
<div class="min-h-[80vh] grid place-items-center px-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-6">
            <img src="{{ asset('img/ferguti-logo-1.png') }}" class="mx-auto h-12 w-12 rounded" alt="Logo">
            <h1 class="mt-3 text-xl font-semibold text-slate-900">Iniciar sesión</h1>
        </div>
        <div class="rounded-2xl bg-white shadow-card ring-1 ring-slate-200 p-5 sm:p-6">
            <h1 class="text-xl font-semibold text-slate-900 mb-2">Recuperar contraseña</h1>
            <p class="text-sm text-slate-500 mb-4">Te enviaremos un enlace para restablecerla.</p>

            @if (session('status'))
            <div class="mb-4 text-sm text-green-600">{{ session('status') }}</div>
            @endif

            <x-validation-errors class="mb-4" />

            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <input type="email" name="email" required autofocus
                    class="block w-full rounded-lg border-slate-300 px-3 py-2">
                <button class="mt-4 w-full rounded-lg bg-indigo-600 py-2.5 text-white">
                    Enviar enlace
                </button>
            </form>
        </div>
        <div class="mt-4 text-center text-sm">
            <a href="{{ route('login') }}" class="text-slate-600 hover:text-slate-800">Volver al inicio de sesión</a>
        </div>
    </div>
</div>
</x-guest-layout>
