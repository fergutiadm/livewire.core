<?php

namespace App\Http\Livewire\Admin;

use App\Models\Moneda;
use App\Services\MonedaService;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

use Livewire\WithPagination;
use Throwable;

class Monedas extends Component
{
    use WithPagination;

    public $codigo;
    public $nombre;
    public $simbolo;
    public $tasa_cambio;
    public $es_principal = false;
    public $activa = false;
    // public $monedas = [];
    public $monedaId;
    public $color_bg = 'bg-white';  // Default color
    public $color_text = 'text-black'; // Default color

    public ?int $monedaIdToDelete = null;
    public bool $confirmingMonedaDeletion = false;

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

    public $perPage = 10; // items por página

    public $formularioVisible = true;

    public function updatingPerPage()
    {
        $this->resetPage(); // resetea la página al cambiar perPage
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


    public function mount()
    {
        //$this->loadMonedas();
    }

    // public function loadMonedas()
    // {
    //     $this->monedas = Moneda::orderBy('es_principal', 'desc')->get();
    // }

    public function save()
    {
        $validated = $this->validate([
            'codigo'       => 'required|string|max:8|unique:monedas,codigo,' . $this->monedaId,
            'nombre'       => 'required|string|max:64',
            'simbolo'      => 'nullable|string|max:8',
            'tasa_cambio'  => 'required|numeric|min:0.000001',
            'es_principal' => 'boolean',
            'activa'       => 'boolean',
            'color_bg' => ['required'],
            'color_text' => ['required'],
        ], [
            'codigo.required'       => 'El código es obligatorio.',
            'codigo.string'         => 'El código debe ser texto.',
            'codigo.max'            => 'El código no puede superar 8 caracteres.',
            'codigo.unique'         => 'Ya existe una moneda con este código.',

            'nombre.required'       => 'El nombre es obligatorio.',
            'nombre.string'         => 'El nombre debe ser texto.',
            'nombre.max'            => 'El nombre no puede superar 64 caracteres.',

            'simbolo.string'        => 'El símbolo debe ser texto.',
            'simbolo.max'           => 'El símbolo no puede superar 8 caracteres.',

            'tasa_cambio.required'  => 'La tasa de cambio es obligatoria.',
            'tasa_cambio.numeric'   => 'La tasa de cambio debe ser un número.',
            'tasa_cambio.min'       => 'La tasa de cambio debe ser mayor que 0.',

            'es_principal.boolean'  => 'El valor de principal es incorrecto.',
            'activa.boolean'        => 'El valor de activa es incorrecto.',

            'color_bg.in'           => 'El color de fondo seleccionado no es válido.',
            'color_text.in'         => 'El color de texto seleccionado no es válido.',

        ]);

        if ($this->es_principal) {
            // Desmarcar todas las demás monedas
            Moneda::where('es_principal', 1)
                  ->when($this->monedaId, fn($q) => $q->where('id', '!=', $this->monedaId))
                  ->update(['es_principal' => 0]);
        }

        if (!$this->paletteIsValid()) {
            $this->addError('color_bg', 'La combinación de colores no es válida.');
            return;
        }

        $data = [
            'codigo'          => $this->codigo,
            'nombre'          => $this->nombre,
            'simbolo'         => $this->simbolo,
            'es_principal'    => $this->es_principal,
            'tasa_cambio'     => $this->tasa_cambio,
            'activa'          => $this->activa,
            'color_bg'        => $this->color_bg,
            'color_text'      => $this->color_text,

        ];

        if ($this->monedaId) {
            // ✏️ Actualizar
            $moneda = Moneda::findOrFail($this->monedaId);
            $moneda->fill($data);
            $moneda->save(); // dispara observer → traza registrada
        } else {
            // ➕ Crear
            Moneda::create($data);
        }

        $this->dispatch('livewire:confirm', [
            'message'  => $this->localId ? 'Moneda actualizada con éxito.' : 'Moneda creada con éxito',
            'type'     => 'success',
        ]);

        $this->resetForm();
        // $this->loadMonedas();

        app(MonedaService::class)->forgetCache();
        $this->dispatch('monedasActualizadas');

    }

    #[On('edit')]
    public function edit($id)
    {
        try{
            $moneda = Moneda::findOrFail($id);

            $this->monedaId      = $moneda->id;
            $this->codigo        = $moneda->codigo;
            $this->nombre        = $moneda->nombre;
            $this->simbolo       = $moneda->simbolo;
            $this->es_principal  = $moneda->es_principal;
            $this->tasa_cambio   = $moneda->tasa_cambio;
            $this->activa        = $moneda->activa;
            $this->color_bg      = $moneda->color_bg ?? 'bg-slate-500';
            $this->color_text    = $moneda->color_text ?? 'text-white';
        }catch(Throwable $e){
            $this->dispatch('livewire:alert',[
                            'message' => 'Ha ocurrido un error. Contacte al administrador del sistema.',
                            'type' => 'error']
                            );
            Log::error('Error en método edit moneda', [
                    'moneda_id'   => $id,
                    'exception'   => $e,
                ]);
            return;
        }
    }

    #[On('confirmDelete')]
    public function confirmDelete(int $id)
    {
        $this->monedaIdToDelete = $id;
        $this->confirmingMonedaDeletion = true;
    }

    public function cancelDelete()
    {
        $this->monedaIdToDelete = null;
    }

    public function delete()
    {
        if (!$this->monedaIdToDelete) return;

        try{
            $moneda = Moneda::findOrFail($this->monedaIdToDelete);

            if ($moneda->es_principal) {
                $this->addError('delete', 'No se puede eliminar la moneda principal.');
                $this->monedaIdToDelete = null;
                return;
            }

            $moneda->delete();
        }catch(Throwable $e){
            $this->dispatch('livewire:alert',[
                            'message' => 'Ha ocurrido un error. Contacte al administrador del sistema.',
                            'type' => 'error']
                            );
            Log::error('Error en método delete monedas', [
                    'moneda_id' => $this->monedaIdToDelete,
                    'exception'   => $e,
                ]);
            return;
        }

        $this->confirmingMonedaDeletion = false;
        usleep(500000);
        $this->dispatch('monedasActualizadas');

        $this->monedaIdToDelete = null;

        // $this->loadMonedas();

        app(MonedaService::class)->forgetCache();
    }


    public function cancel()
    {
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->reset('monedaId', 'nombre', 'codigo', 'simbolo', 'es_principal', 'tasa_cambio', 'activa', 'color_bg', 'color_text');
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.admin.monedas')
                ->layout('layouts.app_admin');
    }
}
