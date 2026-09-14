<header class="bg-white border-b">
    <div class="max-w-7xl mx-auto px-4 h-16 flex justify-between items-center">
        <span class="font-bold text-lg">Livewire.Core</span>

        <div x-data="{ open:false }" class="relative">
            <button @click="open=!open" class="flex items-center gap-2 text-sm">
                <span>{{ auth()->user()->name }}</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <div x-show="open" @click.outside="open=false"
                 class="absolute right-0 mt-2 w-40 bg-white border rounded shadow">
                <a href="#" class="block px-4 py-2 hover:bg-gray-100">Perfil</a>
                <a href="#" class="block px-4 py-2 hover:bg-gray-100">Salir</a>
            </div>
        </div>
    </div>
</header>
