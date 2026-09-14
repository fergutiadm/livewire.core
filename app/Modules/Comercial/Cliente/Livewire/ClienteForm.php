<?php

namespace App\Modules\Comercial\Cliente\Livewire;

use App\Core\CQRS\HasCommands;
use App\Models\Cliente;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Illuminate\Validation\Rule;
use Livewire\Component;

class ClienteForm extends Component
{
    use HasCommands;

    public ?int $clienteId = null;

    public string $nombre = '';

    public string $email = '';

    public string $telefono = '';

    public string $ci = '';

    protected function rules(): array
    {
        return [
            'nombre' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                    'required',
                    'email',
                    Rule::unique('clientes', 'email')->ignore($this->clienteId),
                ],

            'telefono' => [
                        'nullable',
                        'string',
                        'max:255',
                        'regex:/^[0-9+\s().-]+$/',
                    ],

            'ci' => ['digits:11'],
        ];
    }

    protected function messages(): array
    {
        return [
            'nombre.required' =>
                'El Nombre es obligatorio',
            'email.required' =>
                'El correo es obligatorio',
            'email.email' =>
                'El correo no tiene un formato válido',
            'email.unique' =>
                'Este correo ya está registrado.',

            'telefono.max' =>
                'telefono demasiado larga',

            'telefono.regex' =>
                'El teléfono contiene caracteres no válidos',

            'ci.digits' =>
                'Carnet de identidad tiene que ser de 11 digitos',
        ];
    }

    #[On('cliente-cargar-edicion')]
    public function edit(int $id): void
    {
        $cliente = Cliente::findOrFail($id);

        $this->clienteId = $cliente->id;

        $this->nombre = $cliente->nombre;

        $this->email = $cliente->email ?? '';

        $this->telefono = $cliente->telefono ?? '';

        $this->ci = $cliente->ci ?? '';

        // Avisar que terminó la edición
        $this->dispatch(
            'cliente-edicion-cargado',
            clienteId: $cliente->id
        );

        $this->resetValidation();
    }

    #[On('cliente-edicion-cargado')]
    public function clienteEdicionCargada(): void
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

        if ($this->clienteId) {

            $payload = $this->payload();

            //Logger()->info("ACTUALIZANDO PRODUCTO", ['payload'=>$payload]);

            $cliente = $this->command(
                'cliente.update',
                $payload,
            );

            $this->dispatch(
                'cliente-actualizado',
            );

            $this->dispatch(
                'livewire:alert',
                [
                    'message' =>
                        'Cliente Actualizado correctamente...',
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

            $cliente = $this->command(
                'cliente.create',
                $this->payload()
            );

            $this->dispatch(
                'cliente-creado'
            );

            $this->dispatch(
                'livewire:alert',
                [
                    'message' =>
                        'Cliente Creado correctamente...',
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
            'cliente-guardado'
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
            'id' => $this->clienteId,

            'cliente_id' => $this->clienteId,

            'nombre' => $this->nombre,

            'email' => $this->email,

            'telefono' => $this->telefono,

            'ci' => $this->ci,
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
        Log::info('Listener reset-form', [
            'clienteId' => $this->clienteId,
        ]);

        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->reset([
            'clienteId',
            'nombre',
            'email',
            'telefono',
            'ci',
        ]);


        $this->resetValidation();
    }

    public function render()
    {
        return view('modules.comercial.cliente.form');
    }
}
