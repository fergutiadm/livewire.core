@if($showModalCierreMonedas)

    <div
        class="fixed inset-0 z-50 flex items-center justify-center"
        wire:key="cierre-moneda-modal"
    >

        {{-- MODAL --}}
        <div
            class="
                relative
                w-full
                max-w-md
                rounded-xl
                bg-white
                p-6
                shadow-xl
            "
        >

            <h2 class="text-lg font-semibold text-slate-800">
                lista de monedas, monto, comfirmar y detalles(denominaciones)
            </h2>

            <p class="mt-2 text-sm text-gray-600">
                lista de monedas, monto, comfirmar y detalles(denominaciones)
            </p>
        </div>
    </div>

@endif