<x-guest-layout>
    <div class="min-h-[80vh] grid place-items-center px-4">
        <div class="w-full max-w-md">
            <div class="text-center mb-6">
                <img src="{{ asset('img/ferguti-logo-1.png') }}" class="mx-auto h-12 w-12 rounded" alt="Logo">
                <h1 class="mt-3 text-xl font-semibold text-slate-900">Iniciar sesión</h1>
            </div>

            <div class="rounded-2xl bg-white shadow-card ring-1 ring-slate-200 p-5 sm:p-6">
                @if (session('status'))
                    <div class="mb-4 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700 ring-1 ring-green-200">
                        {{ session('status') }}
                    </div>
                @endif

                <x-validation-errors class="mb-4" />

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <label class="block text-sm font-medium text-slate-700">Email</label>
                    <input name="email" type="email" value="{{ old('email') }}" required autofocus
                        class="mt-1 block w-full rounded-lg border-slate-300 px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">

                    <label class="block text-sm font-medium text-slate-700 mt-4">Password</label>
                    <input name="password" type="password" required
                        class="mt-1 block w-full rounded-lg border-slate-300 px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">

                    <div class="mt-4 flex items-center justify-between">
                        <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                            <input type="checkbox" name="remember" class="rounded border-slate-300 text-indigo-600">
                            Recordarme
                        </label>
                        <a href="{{ route('password.request') }}" class="text-sm text-indigo-600 hover:text-indigo-700">
                            ¿Olvidaste tu contraseña?
                        </a>
                    </div>

                    <button class="mt-5 w-full rounded-lg bg-indigo-600 py-2.5 text-white font-medium hover:bg-indigo-700">
                        Iniciar sesión
                    </button>
                </form>
            </div>

            <div class="mt-4 text-center text-sm">
              <a href="{{ route('register') }}" class="text-slate-600 hover:text-slate-800">Registrarme</a>
            </div>
        </div>
    </div>
</x-guest-layout>
