<div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm max-w-xl mx-auto">
    <div class="mb-6 p-4 bg-slate-50 rounded-xl border border-slate-200 flex justify-between items-center">
        <div>
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Monto en Caja Requerido</span>
            <span class="text-xl font-extrabold text-slate-900">${{ number_format($montoInicialDefinido, 2) }}</span>
        </div>
        <div class="text-right">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Estado de Arqueo</span>
            <span class="text-sm font-bold bg-white px-3 py-1 rounded-lg border border-slate-200 block mt-1" 
                  x-data="{ req: {{ $montoInicialDefinido }} }"
                  :class="total == req ? 'text-green-600 border-green-200 bg-green-50/50' : 'text-amber-500 border-amber-200 bg-amber-50/50'"
                  x-text="total == req ? 'Cuadrado ✓' : 'Descuadrado'">
            </span>
        </div>
    </div>

    <form novalidate wire:submit.prevent="save" class="space-y-4"
          x-data="{
              denominaciones: {{ json_encode($denominaciones) }},
              cantidades: @entangle('cantidades').live,
              get total() {
                  return this.denominaciones.reduce((sum, den) => {
                      let qty = parseInt(this.cantidades[den.id]) || 0;
                      return sum + (qty * parseFloat(den.valor));
                  }, 0);
              }
          }">
        
        <div class="divide-y divide-slate-100 border border-slate-200 rounded-xl overflow-hidden bg-white shadow-sm">
            @foreach($denominaciones as $den)
                <div class="grid grid-cols-3 items-center p-3 gap-4" x-data="{ valor: {{ $den['valor'] }}, id: {{ $den['id'] }} }">
                    <div class="text-xs font-semibold text-slate-700 md:text-sm">
                    <div class="text-xs font-semibold text-slate-700 md:text-sm">
                        Billetes de ${{ number_format($den['valor'], 0) }}
                    </div>
                    <div>
                        <input type="number" min="0" 
                               wire:model.live="cantidades.{{ $den['id'] }}"
                               x-model.number="cantidades[id]"
                               class="form-select-responsive w-full text-center border-slate-300 rounded-lg" 
                               placeholder="0">
                    </div>
                    <div class="text-right text-xs font-mono font-bold text-slate-600 md:text-sm">
                        $<span x-text="((cantidades[id] || 0) * valor).toFixed(2)">0.00</span>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="p-4 bg-slate-900 text-white rounded-xl flex justify-between items-center shadow-md">
            <span class="text-sm font-medium tracking-wide">Total Arqueado:</span>
            <span class="text-base font-mono font-black md:text-lg" :class="total == {{ $montoInicialDefinido }} ? 'text-emerald-400' : 'text-amber-400'">
                $<span x-text="total.toFixed(2)">0.00</span>
            </span>
        </div>

        <button type="submit" 
                class="w-full inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 transition disabled:opacity-50 disabled:cursor-not-allowed shadow-sm"
                :disabled="total !== {{ $montoInicialDefinido }}">
            Guardar Cierre de Caja
        </button>
    </form>
</div>