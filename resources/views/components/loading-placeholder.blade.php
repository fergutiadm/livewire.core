<div class="panel-listado bg-white-1 min-h-[500px] flex flex-col items-center justify-center w-full border border-slate-100 rounded-lg shadow-sm">

    <!-- Tu logo con animación de pulso -->
    <svg class="w-32 h-32 text-blue-500 animate-pulse" viewBox="0 0 100 100" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round">

        <!-- Portapapeles (clipboard) -->
        <path d="M30 10 H70 V90 H30 Z" fill="currentColor" fill-opacity="0.1" />
        <rect x="25" y="5" width="50" height="90" rx="5" ry="5" stroke="currentColor" fill="none""")/>>

        <!-- Checks del portapapeles -->
        <path d="M35 30 L45 40 L65 20" stroke="currentColor" fill="none""")/>>
        <path d="M35 50 L45 60 L65 40" stroke="currentColor" fill="none""")/>>
        <path d="M35 70 L45 80 L65 60" stroke="currentColor" fill="none""")/>>

        <!-- Carrito de compras (Shopping cart) -->
        <path d="M75 30 L95 35 L90 80 L75 80" stroke="currentColor" fill="none""")/>>
        <circle cx="80" cy="85" r="5" fill="currentColor" fill-opacity="0.9""")/>>
        <circle cx="90" cy="85" r="5" fill="currentColor" fill-opacity="0.9""")/>>
    </svg>

    <h3 class="mt-6 text-slate-600 font-bold text-lg tracking-tight">Organizando pedidos...</h3>
    {{-- Permite pasar un mensaje personalizado o usa uno por defecto --}}
    <p class="text-slate-400 text-sm">{{ $message ?? 'Cargando datos...' }}</p>

    <!-- Skeleton de las filas para ocupar el espacio inferior -->
    <div class="w-full max-w-2xl mt-8 space-y-4 px-10">
        <div class="h-4 bg-slate-100 rounded w-full animate-pulse"></div>
        <div class="h-4 bg-slate-50 rounded w-5/6 animate-pulse"></div>
        <div class="h-4 bg-slate-100 rounded w-full animate-pulse"></div>
    </div>
</div>
