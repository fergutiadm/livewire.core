<?php

namespace App\Http\Livewire;

use App\Models\Atributo;
use App\Models\AtributoValor;
use App\Models\Categoria;
use App\Models\Producto;
use Livewire\Component;

class SelectAtributoMorph extends Component
{
    public $modelType = null;
    public $modelId = null;
    public $atributoId = '';
    public $atributos = [];
    public $valores = [];
    public $valoresSeleccionados = [];

    // Estado compartido de tarjetas
    public array $cards = [];

    public function mount()
    {
        $this->atributos = $this->cargarAtributos();
        $this->hidratarDesdeModelo();
    }

    protected function hidratarDesdeModelo()
    {
        if (!$this->modelId) return;

        $model = $this->modelType === 'categoria'
            ? Categoria::with('atributosValores.atributo')->find($this->modelId)
            : Producto::with('atributosValores.atributo')->find($this->modelId);

        foreach ($model->atributosValores as $valor) {
            $attr = $valor->atributo;
            $this->cards[$attr->id]['atributo'] = $attr->nombre;
            $this->cards[$attr->id]['valores'][$valor->id] = $valor->valor;
        }
    }

    public function agregarAtributo()
    {
        if (!$this->atributoId || empty($this->valoresSeleccionados)){
            $this->dispatch('livewire:alert', [
                'message' => 'No ha seleccionado aun Valores para este Atributo',
                'type' => 'warning',
            ]);
            return;
        }

        $atributo = Atributo::findOrFail($this->atributoId);

        foreach ($this->valoresSeleccionados as $valorId) {
            $valor = AtributoValor::findOrFail($valorId);
            $this->cards[$atributo->id]['atributo'] = $atributo->nombre;
            $this->cards[$atributo->id]['valores'][$valor->id] = $valor->valor;
        }

        $this->dispatch('cards-updated', $this->cards);

        $this->reset(['atributoId', 'valoresSeleccionados', 'valores']);
    }

    public function cargarAtributos()
    {
        return Atributo::orderBy('orden_visual')->get();
    }

    public function eliminarCard($atributoId)
    {
        unset($this->cards[$atributoId]);
    }

    public function quitarValor($atributoId, $valorId)
    {
        unset($this->cards[$atributoId]['valores'][$valorId]);
        if (empty($this->cards[$atributoId]['valores'])) {
            unset($this->cards[$atributoId]);
        }
    }

    public function atributoCambiado()
    {
        $this->valores = $this->atributoId ? AtributoValor::where('atributo_id', $this->atributoId)->orderBy('orden_visual')->get() : [];
    }

    public function render()
    {
        return view('livewire.select-atributo-morph');
    }
}
