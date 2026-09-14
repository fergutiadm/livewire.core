<?php

namespace App\Observers;

use App\Models\Traits\HasTrazas;
use App\Models\Traza;
use Illuminate\Database\Eloquent\Model;

class TrazaObserver
{
    /**
     * Mapea operaciones a español
     */
    protected array $operacionesEsp = [
        'create'  => 'crear',
        'update'  => 'actualizar',
        'delete'  => 'eliminar',
        'restore' => 'restaurar',
    ];

    protected function isTrazable(Model $model): bool
    {
        return in_array(HasTrazas::class, class_uses_recursive($model));
    }

    public function created(Model $model): void
    {
        if (! $this->isTrazable($model)) return;

        $diff = $this->getDiff($model);

        $model->addTraza($this->operacionesEsp['create'], $diff ?: $this->atributosEnEsp($model->getAttributes()));
    }

    public function updated(Model $model): void
    {
        if (! $this->isTrazable($model)) return;

        $diff = $this->getDiff($model);

        if (empty($diff)) {
            $diff = ['aviso' => 'No se detectaron cambios visibles'];
        }

        $model->addTraza($this->operacionesEsp['update'], $diff);
    }

    public function deleted(Model $model): void
    {
        if (! $this->isTrazable($model)) return;

        if (method_exists($model, 'includeAllOnDelete') && $model->includeAllOnDelete()) {
            $model->addTraza($this->operacionesEsp['delete'], $this->atributosEnEsp($model->getAttributes()));
        } else {
            $diff = $this->getDiff($model);
            $model->addTraza($this->operacionesEsp['delete'], $diff ?: ['aviso' => 'No hay cambios visibles']);
        }
    }

    public function restored(Model $model): void
    {
        if (! $this->isTrazable($model)) return;

        $model->addTraza($this->operacionesEsp['restore']);
    }

    /**
     * Devuelve los cambios entre original y actual, en español
     */
    protected function getDiff(Model $model): array
    {
        $before = $model->getOriginal();
        $after  = $model->getChanges();

        $diff = [];

        foreach ($after as $key => $value) {
            if (array_key_exists($key, $before)) {
                $diff[$this->atributoEnEsp($key)] = [
                    'antes' => $before[$key],
                    'después' => $value,
                ];
            }
        }

        if (method_exists($model, 'cleanTrazaData')) {
            $diff = $model->cleanTrazaData($diff);
        }

        return $diff;
    }

    /**
     * Convierte nombres de atributos a español si es necesario
     */
    protected function atributoEnEsp(string $attr): string
    {
        // Aquí puedes mapear más atributos si quieres traducción automática
        $map = [
            'nombre'       => 'Nombre',
            'descripcion'  => 'Descripción',
            'activo'       => 'Activo',
            'cerrado'      => 'Cerrado',
            'fecha_inicio' => 'Fecha Inicio',
            'fecha_fin'    => 'Fecha Fin',
            'codigo'       => 'Código',
            'precio'       => 'Precio',
            'costo'        => 'Costo',
        ];

        return $map[$attr] ?? $attr;
    }

    /**
     * Convierte todo un array de atributos a español
     */
    protected function atributosEnEsp(array $attrs): array
    {
        return collect($attrs)->mapWithKeys(function ($value, $key) {
            return [$this->atributoEnEsp($key) => $value];
        })->toArray();
    }
}
