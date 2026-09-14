<?php

namespace App\Modules\Finanzas\Moneda\Livewire;

use App\Core\CQRS\HasCommands;
use App\Models\Moneda;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

class MonedaForm extends Component
{
    use HasCommands;

    public ?int $monedaId = null;

    public string $codigo = '';

    public string $nombre = '';

    public string $simbolo = '';

    public bool $es_principal = false;

    public bool $activa = true;

    public string $tasa_cambio = '';

    public $color_bg = 'bg-white';  // Default color

    public $color_text = 'text-black'; // Default color

    public array $palette = [
        ['bg' => 'bg-white',      'text' => 'text-black'],
        ['bg' => 'bg-blue-500',   'text' => 'text-white'],
        ['bg' => 'bg-green-500',  'text' => 'text-white'],
        ['bg' => 'bg-yellow-500', 'text' => 'text-black'],
        ['bg' => 'bg-purple-500', 'text' => 'text-white'],
        ['bg' => 'bg-pink-500',   'text' => 'text-white'],
        ['bg' => 'bg-indigo-500', 'text' => 'text-white'],
        ['bg' => 'bg-teal-500',   'text' => 'text-white'],
        ['bg' => 'bg-orange-500', 'text' => 'text-black'],
        ['bg' => 'bg-cyan-500',   'text' => 'text-black'],
        ['bg' => 'bg-black',      'text' => 'text-white'],
    ];

    protected $rules = [
        'nombre' => 'required|string|max:255',
        'codigo' => 'required|string|min:3|max:10',
        'simbolo' => 'required|string|max:10',
        'es_principal' => 'boolean',
        'tasa_cambio' => 'required|numeric|min:0',
        ];

    protected function messages(): array
    {
        return [
        'nombre.required' => 'El Nombre es obligatorio',

        'codigo.required' => 'El Código es obligatorio',
        'codigo.min' => 'El Código debe tener al menos 3 caracteres',
        'codigo.max' => 'El Código no puede tener más de 10 caracteres',

        'simbolo.required' => 'El Símbolo es obligatorio',
        'simbolo.max' => 'El Símbolo no puede tener más de 10 caracteres',

        'es_principal.boolean' => 'El campo Principal debe ser verdadero o falso',

        'tasa_cambio.required' => 'La Tasa de cambio es obligatoria',
        'tasa_cambio.numeric' => 'La Tasa de cambio debe ser numérica',
        'tasa_cambio.min' => 'La Tasa de cambio no puede ser negativa',
        ];

    }

    public function selectPalette(string $bg, string $text): void
    {
        $this->color_bg = $bg;
        $this->color_text = $text;
    }

    private function paletteIsValid(): bool
    {
        return collect($this->palette)->contains(fn ($p) =>
            $p['bg'] === $this->color_bg && $p['text'] === $this->color_text
        );
    }

    #[On('moneda-cargar-edicion')]
    public function edit(int $id): void
    {
        $moneda = Moneda::findOrFail($id);


        $this->monedaId = $moneda->id;

        $this->nombre = $moneda->nombre;

        $this->codigo = $moneda->codigo ?? '';

        $this->simbolo = $moneda->simbolo;

        $this->es_principal = $moneda->es_principal;

        $this->tasa_cambio = $moneda->tasa_cambio;

        $this->color_bg = $moneda->color_bg;

        $this->color_text = $moneda->color_text;

        // Avisar que terminó la edición
        $this->dispatch(
            'moneda-edicion-cargada',
            monedaId: $moneda->id
        );

        $this->resetValidation();
    }

    #[On('moneda-edicion-cargada')]
    public function monedaEdicionCargada(): void
    {
        $this->dispatch('loading-stop');
    }

    /*
    |--------------------------------------------------------------------------
    | Guardar
    |--------------------------------------------------------------------------
    */

    public function save(): void
    {
        try {
            $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('loading-stop');

            throw $e;
        }

        /*
        |--------------------------------------------------------------------------
        | UPDATE
        |--------------------------------------------------------------------------
        */

        if ($this->monedaId) {

            $payload = $this->payload();

            //Logger()->info("ACTUALIZANDO PRODUCTO", ['payload'=>$payload]);

            $moneda = $this->command(
                'moneda.update',
                $payload,
            );

            $this->dispatch(
                'moneda-actualizada',
            );

            $this->dispatch(
                'livewire:alert',
                [
                    'message' =>
                        'Moneda Actualizada correctamente...',
                    'type' => 'success',
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | CREATE
        |--------------------------------------------------------------------------
        */

        else {

            $moneda = $this->command(
                'moneda.create',
                $this->payload()
            );

            $this->dispatch(
                'moneda-creado'
            );

            $this->dispatch(
                'livewire:alert',
                [
                    'message' =>
                        'Moneda Creada correctamente...',
                    'type' => 'success',
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Notificar página
        |--------------------------------------------------------------------------
        */

        $this->dispatch(
            'moneda-guardada'
        );

        $this->dispatch('loading-stop');

        /*
        |--------------------------------------------------------------------------
        | Reset
        |--------------------------------------------------------------------------
        */

        $this->resetForm();
    }

    /*
    |--------------------------------------------------------------------------
    | Payload CQRS
    |--------------------------------------------------------------------------
    */

    private function payload(): array
    {
        return [
            'id' => $this->monedaId,

            'moneda_id' => $this->monedaId,

            'nombre' => $this->nombre,

            'codigo' => $this->codigo,

            'simbolo' => $this->simbolo,

            'es_principal' => $this->es_principal,

            'activa' => $this->activa,

            'tasa_cambio' => $this->tasa_cambio,

            'color_bg' => $this->color_bg,

            'color_text' => $this->color_text,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Cancelar
    |--------------------------------------------------------------------------
    */

    public function cancel(): void
    {
        $this->resetForm();

        $this->dispatch(
            'livewire:alert',
            [
                'message' =>
                    'Acción cancelada...',
                'type' =>
                    'warning',
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Reset formulario
    |--------------------------------------------------------------------------
    */

    #[On('reset-form')]
    public function resetFormEvent(): void
    {
        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->reset([
            'monedaId',
            'nombre',
            'codigo',
            'simbolo',
            'es_principal',
            'activa',
            'tasa_cambio',
            'color_bg',
            'color_text',
        ]);

        $this->resetValidation();
    }

    public function render()
    {
        return view('modules.finanzas.moneda.form');
    }
}
