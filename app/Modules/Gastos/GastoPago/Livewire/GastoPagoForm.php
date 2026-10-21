<?php

namespace App\Modules\Gastos\GastoPago\Livewire;

use App\Models\Gasto;
use App\Models\Moneda;
use App\Models\TarjetaMagnetica;
use App\Services\GastoPagoService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class GastoPagoForm extends Component
{
    public ?int $pagoId = null;

    public ?int $gastoId = null;

    public ?Gasto $gasto = null;

    public string $fechaPago = '';

    public ?string $descripcion = null;

    public array $desgloses = [];

    public function mount(?int $gastoId = null): void
    {
        $this->gastoId = $gastoId;

        if ($this->gastoId) {
            $this->gasto = Gasto::findOrFail($this->gastoId);
        }

        $this->fechaPago = now()->format('Y-m-d H:i:s');

        $this->agregarDesglose();
    }

    public function confirmar(): void
    {
        if (!$this->pagoId) {
            
            $this->dispatch(
                'livewire:alert',
                [
                    'message' =>
                        'Primero debe guardarse el pago como borrador.',
                    'type' => 'warning',
                ]
            );
            
            //$this->saveDraft();

            return;
        }

        $pago = \App\Models\GastoPago::findOrFail(
            $this->pagoId
        );

        app(GastoPagoService::class)->confirmar($pago);

        $this->dispatch(
            'gasto-pago-confirmado',
            pagoId: $pago->id,
        );
    }

    public function agregarDesglose(): void
    {
        $this->desgloses[] = [
            'moneda_id' => null,
            'cantidad' => '',
            'tasa_cambio' => '',
            'metodo' => GastoPagoService::METODO_EFECTIVO,
            'tarjeta_magnetica_id' => null,
            'numero_tarjeta' => null,
        ];
    }

    public function eliminarDesglose(int $index): void
    {
        if (count($this->desgloses) <= 1) {
            return;
        }

        unset($this->desgloses[$index]);

        $this->desgloses = array_values($this->desgloses);
    }

    public function metodoChanged(int $index): void
    {
        if (!isset($this->desgloses[$index])) {
            return;
        }

        if (
            $this->desgloses[$index]['metodo']
            === GastoPagoService::METODO_EFECTIVO
        ) {
            $this->desgloses[$index]['tarjeta_magnetica_id'] = null;
            $this->desgloses[$index]['numero_tarjeta'] = null;
        }
    }

    public function tarjetaChanged(int $index): void
    {
        if (!isset($this->desgloses[$index])) {
            return;
        }

        $tarjetaId =
            $this->desgloses[$index]['tarjeta_magnetica_id'];

        if (!$tarjetaId) {
            $this->desgloses[$index]['numero_tarjeta'] = null;

            return;
        }

        $tarjeta = TarjetaMagnetica::find($tarjetaId);

        if (!$tarjeta) {
            return;
        }

        $this->desgloses[$index]['numero_tarjeta']
            = $tarjeta->numero;
    }

    public function calcularTotalCup(): float
    {
        return collect($this->desgloses)
            ->sum(function (array $desglose) {
                $cantidad = (float) ($desglose['cantidad'] ?? 0);
                $tasa = (float) ($desglose['tasa_cambio'] ?? 0);

                return $cantidad * $tasa;
            });
    }

    public function saveDraft(): void
    {
        $this->validate([
            'gastoId' => ['required', 'integer', 'exists:gastos,id'],
            'fechaPago' => ['required', 'date'],
            'descripcion' => ['nullable', 'string', 'max:255'],

            'desgloses' => ['required', 'array', 'min:1'],

            'desgloses.*.moneda_id' => [
                'required',
                'integer',
                'exists:monedas,id',
            ],

            'desgloses.*.cantidad' => [
                'required',
                'numeric',
                'gt:0',
            ],

            'desgloses.*.tasa_cambio' => [
                'required',
                'numeric',
                'gt:0',
            ],

            'desgloses.*.metodo' => [
                'required',
                'in:efectivo,tarjeta',
            ],

            'desgloses.*.tarjeta_magnetica_id' => [
                'nullable',
                'integer',
                'exists:tarjetas_magneticas,id',
            ],

            'desgloses.*.numero_tarjeta' => [
                'nullable',
                'string',
                'max:20',
            ],
        ]);

        $pago = app(GastoPagoService::class)->crearDraft([
            'gasto_id' => $this->gastoId,
            'fecha_pago' => $this->fechaPago,
            'operador_id' => Auth::id(),
            'descripcion' => $this->descripcion,
            'desgloses' => $this->desgloses,
        ]);

        $this->pagoId = $pago->id;

        $this->dispatch(
            'gasto-pago-creado',
            pagoId: $pago->id,
        );
    }

    public function render()
    {
        return view('modules.gastos.gasto-pago.form', [
            'monedas' => Moneda::query()
                ->where('activa', true)
                ->orderByDesc('es_principal')
                ->orderBy('codigo')
                ->get(),

            'tarjetas' => TarjetaMagnetica::query()
                ->with('moneda')
                ->orderBy('numero')
                ->get(),
        ]);
    }
}