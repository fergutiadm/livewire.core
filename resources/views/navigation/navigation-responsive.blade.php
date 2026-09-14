<header class="bg-white border-b" x-data="{ open:false }">
    <div class="max-w-7xl mx-auto px-4 h-16 flex justify-between items-center">
        <span class="font-bold">Livewire.Core</span>

        <button @click="open=!open" class="md:hidden">
            ☰
        </button>

        <nav class="hidden md:flex gap-6 text-sm">
            <a href="#">Dashboard</a>
            <a href="#">Productos</a>
            <a href="#">Ventas</a>
        </nav>
    </div>

    <div x-show="open" class="md:hidden border-t">
        <nav class="flex flex-col">
            <a class="px-4 py-2 hover:bg-gray-100">Dashboard</a>
            <a class="px-4 py-2 hover:bg-gray-100">Productos</a>
            <a class="px-4 py-2 hover:bg-gray-100">Ventas</a>
        </nav>
    </div>
</header>
