<header class="bg-white border-b">
    <div class="max-w-7xl mx-auto px-4 h-16 flex items-center justify-between">
        <div class="flex items-center gap-6">
            <span class="font-bold text-lg">Livewire.Core</span>

            <input
                type="text"
                placeholder="Buscar..."
                class="hidden md:block px-3 py-1 border rounded text-sm"
            >
        </div>

        <div class="flex items-center gap-4">
            <button class="text-sm px-3 py-1 border rounded">
                + Nuevo
            </button>
            <span class="text-sm text-gray-600">{{ auth()->user()->name }}</span>
        </div>
    </div>
</header>
