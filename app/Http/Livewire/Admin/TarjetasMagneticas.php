<?php

namespace App\Http\Livewire\Admin;

use App\Models\Moneda;
use App\Models\TarjetaMagnetica;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

use Livewire\WithPagination;
use Throwable;

class TarjetasMagneticas extends Component
{
    use WithPagination;

    public $monedaId;
    public $monedas;
    public $numero;
    public $propietario;
    // public $tarjetas = [];
    public $tarjetaId;
    public $color_bg = 'bg-white';
    public $color_text = 'text-black';

    public ?int $tarjetaIdToDelete = null;
    public bool $confirmingTarjetaDeletion = false;

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
        $this->loadMonedas();
    }

    public function loadMonedas()
    {
        $this->monedas = Moneda::orderBy('es_principal', 'desc')->orderBy('nombre')->get();
    }

    public function save()
    {
        $validated = $this->validate([
            'numero'            => 'required|unique:tarjetas_magneticas,numero',
            'propietario'       => 'required|string|max:64',
            'monedaId'          => 'required|exists:monedas,id',
            'color_bg'          => ['required'],
            'color_text'        => ['required'],
        ], [
            'numero.required'            => 'El número es obligatorio.',
            // 'numero.string'              => 'El código debe ser texto.',
            'numero.unique'              => 'Ya existe una tarjeta con este número.',

            'propietario.required'       => 'El nombre es obligatorio.',
            'propietario.string'         => 'El nombre debe ser texto.',
            'propietario.max'            => 'El nombre no puede superar 64 caracteres.',

            'monedaId.required'          => 'La Moneda es obligatoria',

            'color_bg.in'                => 'El color de fondo seleccionado no es válido.',
            'color_text.in'              => 'El color de texto seleccionado no es válido.',

        ]);

        // if ($this->es_principal) {
        //     // Desmarcar todas las demás tarjetas
        //     TarjetaMagnetica::where('es_principal', 1)
        //           ->when($this->tarjetaId, fn($q) => $q->where('id', '!=', $this->tarjetaId))
        //           ->update(['es_principal' => 0]);
        // }

        if (!$this->paletteIsValid()) {
            $this->addError('color_bg', 'La combinación de colores no es válida.');
            return;
        }

        $data = [
            'numero'               => $this->numero,
            'propietario'          => $this->propietario,
            'color_bg'             => $this->color_bg,
            'color_text'           => $this->color_text,

        ];
        try{
            if ($this->tarjetaId) {
                // ✏️ Actualizar
                $tarjeta = TarjetaMagnetica::findOrFail($this->tarjetaId);
                $tarjeta->fill($data);
                $tarjeta->save(); // dispara observer → traza registrada
            } else {
                // ➕ Crear
                TarjetaMagnetica::create($data);
            }
        }catch(Throwable $e){
            $this->dispatch('livewire:alert',[
                            'message' => 'Ha ocurrido un error. Contacte al administrador del sistema.',
                            'type' => 'error']
                            );
            Log::error('Error en método save TarjetaMagnetica', [
                    'tarjeta_id'  => $this->tarjetaId,
                    'exception'   => $e,
                ]);
        }

        $this->dispatch('livewire:confirm', [
            'message'  => $this->tarjetaId ? 'Tarjeta actualizada con éxito.' : 'Tarjeta creada con éxito',
            'type'     => 'success',
        ]);

        $this->resetForm();
        // $this->loadTarjetasMagneticas();

        $this->dispatch('tarjetasActualizadas');

    }

    #[On('edit')]
    public function edit($id)
    {
        try{
            $tarjeta = TarjetaMagnetica::findOrFail($id);

            $this->tarjetaId     = $tarjeta->id;
            $this->numero        = $tarjeta->numero;
            $this->propietario   = $tarjeta->propietario;
            $this->color_bg      = $tarjeta->color_bg ?? 'bg-slate-500';
            $this->color_text    = $tarjeta->color_text ?? 'text-white';
        }catch(Throwable $e){
            $this->dispatch('livewire:alert',[
                            'message' => 'Ha ocurrido un error. Contacte al administrador del sistema.',
                            'type' => 'error']
                            );
            Log::error('Error en método edit TarjetaMagnetica', [
                    'tarjeta_id'  => $id,
                    'exception'   => $e,
                ]);
        }
    }

    #[On('confirmDelete')]
    public function confirmDelete(int $id)
    {
        $this->tarjetaIdToDelete = $id;
        $this->confirmingTarjetaDeletion = true;
    }

    public function cancelDelete()
    {
        $this->tarjetaIdToDelete = null;
    }

    public function delete()
    {
        if (!$this->tarjetaIdToDelete) return;

        try{
            $tarjeta = TarjetaMagnetica::findOrFail($this->tarjetaIdToDelete);

            $tarjeta->delete();


            // $this->loadTarjetasMagneticas();
            $this->dispatch('tarjetasActualizadas');
        }catch(Throwable $e){
            $this->dispatch('livewire:alert',[
                            'message' => 'Ha ocurrido un error. Contacte al administrador del sistema.',
                            'type' => 'error']
                            );
            Log::error('Error en método delete TarjetasMagneticas', [
                    'tarjeta_id'  => $this->tarjetaIdToDelete,
                    'exception'   => $e,
                ]);
        }

        $this->confirmingTarjetaDeletion = false;
        usleep(500000);
        $this->dispatch('tarjetasActualizadas');

        $this->tarjetaIdToDelete = null;
    }


    public function cancel()
    {
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->reset('tarjetaId', 'monedaId', 'numero', 'propietario');
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.admin.tarjetas_magneticas')
                ->layout('layouts.app_admin');
    }
}
