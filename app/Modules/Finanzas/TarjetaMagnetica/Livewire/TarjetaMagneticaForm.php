<?php

    namespace App\Modules\Finanzas\TarjetaMagnetica\Livewire;

    use App\Core\CQRS\HasCommands;
    use App\Models\Moneda;
    use App\Models\TarjetaMagnetica;
    use Illuminate\Support\Facades\Log;
    use Livewire\Attributes\On;
    use Livewire\Component;

    class TarjetaMagneticaForm extends Component
    {
        use HasCommands;

        public ?int $tarjetaMagneticaId = null;

        public ?int $monedaId = null;

        public string $numero = '';

        public string $propietario = '';

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
            'propietario' => 'required|string|max:255',
            'numero' => 'required|digits:16',
            'monedaId' => 'required|exists:monedas,id',
            ];

        public function mount(): void
        {
            $this->monedaId =
                Moneda::orderByDesc('es_principal')
                    ->value('id');
        }

        protected function messages(): array
        {
            return [
                'propietario.required' => 'El propietario es obligatorio',

                'monedaId.required' => 'La moneda es obligatoria',

                'numero.digits' => 'El número debe contener exactamente 16 dígitos',

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

        #[On('tarjeta-magnetica-cargar-edicion')]
        public function edit(int $id): void
        {
            $tarjetaMagnetica = tarjetaMagnetica::findOrFail($id);


            $this->tarjetaMagneticaId = $tarjetaMagnetica->id;

            $this->propietario = $tarjetaMagnetica->propietario;

            $this->numero = $this->formatearNumero($tarjetaMagnetica->numero ?? '');

            $this->monedaId = $tarjetaMagnetica->moneda_id;

            $this->color_bg = $tarjetaMagnetica->color_bg;

            $this->color_text = $tarjetaMagnetica->color_text;

            // Avisar que terminó la edición
            $this->dispatch(
                'tarjeta-magnetica-edicion-cargada',
                tarjetaMagneticaId: $tarjetaMagnetica->id
            );

            $this->resetValidation();
        }

        #[On('tarjeta-magnetica-edicion-cargada')]
        public function tarjetaMagneticaEdicionCargada(): void
        {
            $this->dispatch('loading-stop');
        }

        private function formatearNumero(string $numero): string
        {
            $numero = preg_replace('/\D/', '', $numero);

            return implode('-', str_split($numero, 4));

        }

        /*
        |--------------------------------------------------------------------------
        | Guardar
        |--------------------------------------------------------------------------
        */

        public function save(): void
        {
            /**
            |--------------------------------------------------------------------------
            | Normalizar número
            |--------------------------------------------------------------------------
            */

            $this->numero = preg_replace('/\D/', '', $this->numero);

            /**
            |--------------------------------------------------------------------------
            | Validar
            |--------------------------------------------------------------------------
            */

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

            if ($this->tarjetaMagneticaId) {

                $payload = $this->payload();

                //Logger()->info("ACTUALIZANDO PRODUCTO", ['payload'=>$payload]);

                $tarjetaMagnetica = $this->command(
                    'tarjetaMagnetica.update',
                    $payload,
                );

                $this->dispatch(
                    'tarjeta-magnetica-actualizada',
                );

                $this->dispatch(
                    'livewire:alert',
                    [
                        'message' =>
                            'tarjetaMagnetica Actualizada correctamente...',
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

                $tarjetaMagnetica = $this->command(
                    'tarjetaMagnetica.create',
                    $this->payload()
                );

                $this->dispatch(
                    'tarjetaMagnetica-creada'
                );

                $this->dispatch(
                    'livewire:alert',
                    [
                        'message' =>
                            'tarjetaMagnetica Creada correctamente...',
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
                'tarjeta-magnetica-guardada'
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
                'id' => $this->tarjetaMagneticaId,

                'propietario' => $this->propietario,

                'numero' => $this->numero,

                'moneda_id' => $this->monedaId,

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
                'tarjetaMagneticaId',
                'propietario',
                'numero',
                'color_bg',
                'color_text',
            ]);

            $this->monedaId = Moneda::orderByDesc('es_principal')->value('id');

            $this->resetValidation();
        }

        public function render()
        {
            $monedas =
                Moneda::orderByDesc(
                    'es_principal'
                )->get();

            return view('modules.finanzas.tarjeta-magnetica.form',
            [
                'monedas' =>
                    $monedas,
            ]);
        }

    }
