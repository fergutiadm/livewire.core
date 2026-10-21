<?php
/**
 * Generador automático para el submódulo de Cierre Diario (PHP 8.1 / Laravel 10)
 */

$archivos = [
    // 1. MIGRACIÓN: CONTENEDOR PRINCIPAL DEL ARQUEO
    "database/migrations/2026_10_07_000002_create_cierre_diario_desgloses_table.php" => '<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(\'cierre_diario_desgloses\', function (Blueprint $table) {
            $table->id();
            $table->foreignId(\'local_id\')->constrained(\'locales\');
            $table->foreignId(\'periodo_contable_id\')->constrained(\'periodos_contables\');
            $table->foreignId(\'operador_id\')->constrained(\'users\');
            $table->decimal(\'monto_inicial_definido\', 12, 2);
            $table->decimal(\'monto_desglosado_total\', 12, 2);
            $table->date(\'fecha\');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(\'cierre_diario_desgloses\');
    }
};',

    // 2. MIGRACIÓN: DETALLES POR BILLETE
    "database/migrations/2026_10_07_000003_create_cierre_detalles_table.php" => '<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(\'cierre_detalles\', function (Blueprint $table) {
            $table->id();
            $table->foreignId(\'cierre_diario_desglose_id\')->constrained(\'cierre_diario_desgloses\')->cascadeOnDelete();
            $table->foreignId(\'denominacion_id\')->constrained(\'denominaciones\');
            $table->integer(\'cantidad_billetes\')->default(0);
            $table->decimal(\'subtotal\', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(\'cierre_detalles\');
    }
};',

    // 3. MODELO PRINCIPAL
    "app/Models/CierreDiarioDesglose.php" => '<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Traits\HasTrazas;

class CierreDiarioDesglose extends Model
{
    use HasFactory, HasTrazas;

    protected $table = \'cierre_diario_desgloses\';

    protected $fillable = [
        \'local_id\',
        \'periodo_contable_id\',
        \'operador_id\',
        \'monto_inicial_definido\',
        \'monto_desglosado_total\',
        \'fecha\',
    ];

    public function detalles(): HasMany
    {
        return $this->hasMany(CierreDetalle::class, \'cierre_diario_desglose_id\');
    }
}',

    // 4. MODELO DETALLE
    "app/Models/CierreDetalle.php" => '<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CierreDetalle extends Model
{
    protected $table = \'cierre_detalles\';

    protected $fillable = [
        \'cierre_diario_desglose_id\',
        \'denominacion_id\',
        \'cantidad_billetes\',
        \'subtotal\',
    ];

    public function denominacion(): BelongsTo
    {
        return $this->belongsTo(Denominacion::class, \'denominacion_id\');
    }
}',

    // 5. LIVEWIRE - PAGE
    "app/Modules/Admin/Cierre/Livewire/CierrePage.php" => '<?php

namespace App\Modules\Admin\Cierre\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Core\CQRS\HasCommands;

class CierrePage extends Component
{
    use HasCommands;

    public bool $showForm = true;
    public int $tableVersion = 0;

    #[On(\'cierre-guardado\')]
    public function onSaved(): void
    {
        $this->tableVersion++;
    }

    public function render()
    {
        return view(\'modules.admin.cierre.page\', [
            \'tableVersion\' => $this->tableVersion,
        ])->layout(\'layouts.app_admin\');
    }
}',

    // 6. LIVEWIRE - FORM
    "app/Modules/Admin/Cierre/Livewire/CierreForm.php" => '<?php

namespace App\Modules\Admin\Cierre\Livewire;

use App\Core\CQRS\HasCommands;
use App\Models\Denominacion;
use App\Core\Context\LocalContext;
use App\Core\Context\PeriodoContext;
use App\Models\Gasto;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CierreForm extends Component
{
    use HasCommands;

    public float $montoInicialDefinido = 0.00;
    public array $cantidades = [];
    public array $denominaciones = [];

    public function mount(): void
    {
        $localId = app(LocalContext::class)->id();
        $periodoId = app(PeriodoContext::class)->id();

        $totalVentas = 5000.00; // Temporal provisional

        $totalGastos = Gasto::where(\'local_id\', $localId)
            ->where(\'periodo_contable_id\', $periodoId)
            ->where(\'chequeado\', true)
            ->get()
            ->sum(fn($g) => (float)$g->importe_calculado);

        $this->montoInicialDefinido = max(0, $totalVentas - $totalGastos);
        $this->cargarDenominaciones();
    }

    public function cargarDenominaciones(): void
    {
        $this->denominaciones = Denominacion::where(\'activa\', true)
            ->orderByDesc(\'valor\')
            ->get()
            ->toArray();

        foreach ($this->denominaciones as $den) {
            $this->cantidades[$den[\'id\']] = 0;
        }
    }

    public function getMontoDesglosadoTotalProperty(): float
    {
        $total = 0;
        foreach ($this->denominaciones as $den) {
            $qty = (int) ($this->cantidades[$den[\'id\']] ?? 0);
            $total += $qty * (float) $den[\'valor\'];
        }
        return $total;
    }

    public function save(): void
    {
        $totalDesglosado = $this->montoDesglosadoTotal;

        if (number_format($totalDesglosado, 2, \'.\', \'\') !== number_format($this->montoInicialDefinido, 2, \'.\', \'\')) {
            $this->dispatch(\'livewire:alert\', [
                \'type\' => \'error\',
                \'message\' => \'El total físico no coincide con el monto esperado en caja.\'
            ]);
            return;
        }

        $this->command(\'cierre.create\', [
            \'local_id\' => app(LocalContext::class)->id(),
            \'periodo_contable_id\' => app(PeriodoContext::class)->id(),
            \'operador_id\' => Auth::id(),
            \'monto_inicial_definido\' => $this->montoInicialDefinido,
            \'monto_desglosado_total\' => $totalDesglosado,
            \'fecha\' => now()->format(\'Y-m-d\'),
            \'cantidades\' => $this->cantidades,
            \'denominaciones\' => $this->denominaciones
        ]);

        $this->dispatch(\'livewire:alert\', [\'type\' => \'success\', \'message\' => \'Cierre de caja procesado con éxito\']);
        $this->dispatch(\'cierre-guardado\');
        $this->reset([\'cantidades\']);
        $this->cargarDenominaciones();
    }

    public function render()
    {
        return view(\'modules.admin.cierre.form\');
    }
}',

    // 7. BLADE - PAGE
    "resources/views/modules/admin/cierre/page.blade.php" => '<div
    x-data="{ loading: false, loadingMessage: \'\' }"
    x-on:loading-start.window="loading = true; loadingMessage = $event.detail.message ?? \'Cargando...\';"
    x-on:loading-stop.window="loading = false; loadingMessage = \'\';"
    class="relative"
>
    <x-ui.crud.page>
        <x-slot name="header">
            <x-ui.crud.header title="Cierre Diario de Caja" description="Arqueo físico de billetes y validación de saldo contable" :show-form="$showForm" :show-toggle="false" />
        </x-slot>
        <x-slot name="form">
            <x-ui.crud.form :show="$showForm">
                <livewire:admin.cierre.form />
            </x-ui.crud.form>
        </x-slot>
    </x-ui.crud.page>
    <x-ui.loading-overlay />
</div>',

    // 8. BLADE - FORM
    "resources/views/modules/admin/cierre/form.blade.php" => '<div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm max-w-xl mx-auto">
    <div class="mb-6 p-4 bg-slate-50 rounded-xl border border-slate-200 flex justify-between items-center">
        <div>
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Monto en Caja Requerido</span>
            <span class="text-xl font-extrabold text-slate-900">${{ number_format($montoInicialDefinido, 2) }}</span>
        </div>
        <div class="text-right">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Estado de Arqueo</span>
            <span class="text-sm font-bold bg-white px-3 py-1 rounded-lg border border-slate-200 block mt-1" 
                  x-data="{ req: {{ $montoInicialDefinido }} }"
                  :class="total == req ? \'text-green-600 border-green-200 bg-green-50/50\' : \'text-amber-500 border-amber-200 bg-amber-50/50\'"
                  x-text="total == req ? \'Cuadrado ✓\' : \'Descuadrado\'">
            </span>
        </div>
    </div>

    <form novalidate wire:submit.prevent="save" class="space-y-4"
          x-data="{
              denominaciones: {{ json_encode($denominaciones) }},
              cantidades: @entangle(\'cantidades\').live,
              get total() {
                  return this.denominaciones.reduce((sum, den) => {
                      let qty = parseInt(this.cantidades[den.id]) || 0;
                      return sum + (qty * parseFloat(den.valor));
                  }, 0);
              }
          }">
        
        <div class="divide-y divide-slate-100 border border-slate-200 rounded-xl overflow-hidden bg-white shadow-sm">
            @foreach($denominaciones as $den)
                <div class="grid grid-cols-3 items-center p-3 gap-4" x-data="{ valor: {{ $den[\'valor\'] }}, id: {{ $den[\'id\'] }} }">
                    <div class="text-xs font-semibold text-slate-700 md:text-sm">
                    <div class="text-xs font-semibold text-slate-700 md:text-sm">
                        Billetes de ${{ number_format($den[\'valor\'], 0) }}
                    </div>
                    <div>
                        <input type="number" min="0" 
                               wire:model.live="cantidades.{{ $den[\'id\'] }}"
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
            <span class="text-base font-mono font-black md:text-lg" :class="total == {{ $montoInicialDefinido }} ? \'text-emerald-400\' : \'text-amber-400\'">
                $<span x-text="total.toFixed(2)">0.00</span>
            </span>
        </div>

        <button type="submit" 
                class="w-full inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 transition disabled:opacity-50 disabled:cursor-not-allowed shadow-sm"
                :disabled="total !== {{ $montoInicialDefinido }}">
            Guardar Cierre de Caja
        </button>
    </form>
</div>'
];

echo "🏗️ Iniciando generación en PHP 8.1 del módulo de Cierre Diario...\n";
foreach ($archivos as $ruta => $contenido) {
    $dir = dirname($ruta);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    file_put_contents($ruta, $contenido);
    echo "  ✅ Creado: {$ruta}\n";
}
echo "\n🚀 ¡Módulo de Cierre generado con éxito! Ejecuta: php artisan migrate\n";
