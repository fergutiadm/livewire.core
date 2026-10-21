<?php

namespace App\Modules\Admin\Denominacion\Livewire;

use App\Core\CQRS\HasCommands;
use App\Models\Denominacion;
use App\Models\Moneda;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

class DenominacionForm extends Component
{
    use HasCommands;

    public ?int $denominacionId = null;

    public ?int $monedaId = null;

    public string $valor = '';

    public bool $activa = true;

    public $color_bg = 'bg-white';  // Default color

    public $color_text = 'text-black'; // Default color

    public array $palette = [
        ['bg' => 'bg-white',      'text' => 'text-black'],
        ['bg' => 'bg-blue-500',   'text' => 'text-white'],
        ['bg' => 'bg-green-500',  'text' => 'text-white'],
        ['bg' => 'bg-yellow-500', 'text' => 'text-black'],
        ['bg' => 'bg-yellow-950', 'text' => 'text-white'],
        ['bg' => 'bg-purple-300', 'text' => 'text-purple-500'],
        ['bg' => 'bg-pink-500',   'text' => 'text-white'],
        ['bg' => 'bg-indigo-500', 'text' => 'text-white'],
        ['bg' => 'bg-teal-500',   'text' => 'text-white'],
        ['bg' => 'bg-orange-500', 'text' => 'text-black'],
        ['bg' => 'bg-cyan-500',   'text' => 'text-black'],
        ['bg' => 'bg-black',      'text' => 'text-white'],
    ];

    protected $rules = [
        'monedaId' => 'required|exists:monedas,id',

        'valor' => 'required|numeric|min:1',
        ];

    protected function messages(): array
    {
        return [
        'monedaId.required' => 'La Moneda es obligatoria',

        'valor.required' => 'El Valor es obligatorio',
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

    #[On('denominacion-cargar-edicion')]
    public function edit(int $id): void
    {
        $denominacion = Denominacion::findOrFail($id);


        $this->denominacionId = $denominacion->id;

        $this->monedaId = $denominacion->moneda_id;

        $this->valor = $denominacion->valor;

        $this->activa = $denominacion->activa;

        $this->color_bg = $denominacion->color_bg;

        $this->color_text = $denominacion->color_text;

        // Avisar que terminó la edición
        $this->dispatch(
            'denominacion-edicion-cargada',
            denominacionId: $denominacion->id
        );

        $this->resetValidation();
    }

    #[On('denominacion-edicion-cargada')]
    public function denominacionEdicionCargada(): void
    {
        $this->dispatch('loading-stop');
    }

    /*
    |--------------------------------------------------------------------------
    | Cambio de Moneda
    |--------------------------------------------------------------------------
    */

    public function monedaChanged(): void
    {
        $this->dispatch(
            'loading-start',
            message: 'Cargando denominaciones...'
        );

        $this->dispatch(
            'denominacion-filtros-actualizados',
            monedaId: $this->monedaId,
        );

        $this->resetForm();
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

        if ($this->denominacionId) {

            $payload = $this->payload();

            //Logger()->info("ACTUALIZANDO PRODUCTO", ['payload'=>$payload]);

            $denominacion = $this->command(
                'denominacion.update',
                $payload,
            );

            $this->dispatch(
                'denominacion-actualizada',
            );

            $this->dispatch(
                'livewire:alert',
                [
                    'message' =>
                        'Denominacion Actualizada correctamente...',
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

            $denominacion = $this->command(
                'denominacion.create',
                $this->payload()
            );

            $this->dispatch(
                'denominacion-creado'
            );

            $this->dispatch(
                'livewire:alert',
                [
                    'message' =>
                        'Denominacion Creada correctamente...',
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
            'denominacion-guardada'
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
            'id' => $this->denominacionId,

            'moneda_id' => $this->monedaId,

            'valor' => $this->valor,

            'activa' => $this->activa,

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
            'denominacionId',
            'valor',
            'activa',
            'color_bg',
            'color_text',
        ]);

        if(!$this->monedaId){
            $this->monedaId = Moneda::paraSelect()->value('id');
        }

        $this->resetValidation();
    }
    
    public function render()
    {
        $monedas = Moneda::paraSelect()->get();

        if(!$this->monedaId){
            $this->monedaId = $monedas->first()->id;
        }

        return view('modules.admin.denominacion.form', 
                    ['monedas' => $monedas]
                );
    }
}