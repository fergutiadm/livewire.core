<?php

namespace Tests\Unit\Modules\Inventario\ProductoVariante;

use App\Models\Atributo;
use App\Models\AtributoValor;
use App\Models\Categoria;
use App\Models\Local;
use App\Models\Moneda;
use App\Models\Producto;
use App\Modules\Inventario\ProductoVariante\Services\GeneradorClaveCombinacion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GeneradorClaveCombinacionTest extends TestCase
{
    use RefreshDatabase;

    public function test_genera_la_misma_clave_independientemente_del_orden_de_entrada(): void
    {
        $local = Local::factory()->create();

        $categoria = Categoria::factory()->create([
            'local_id' => $local->id,
        ]);

        $moneda = Moneda::query()->create([
            'codigo' => 'CUP',
            'nombre' => 'Peso Cubano',
            'simbolo' => '$',
            'es_principal' => true,
            'tasa_cambio' => 1,
            'activa' => true,
            'color_bg' => 'bg-blue-500',
            'color_text' => 'text-white',
        ]);

        $producto = Producto::factory()->create([
            'local_id' => $local->id,
            'categoria_id' => $categoria->id,
            'moneda_id' => $moneda->id,
        ]);

        $atributoColor = Atributo::query()->create([
            'codigo' => 'COLOR',
            'nombre' => 'Color',
            'activo' => true,
        ]);

        $atributoTalla = Atributo::query()->create([
            'codigo' => 'TALLA',
            'nombre' => 'Talla',
            'activo' => true,
        ]);

        $valorRojo = AtributoValor::query()->create([
            'atributo_id' => $atributoColor->id,
            'codigo' => 'ROJO',
            'nombre' => 'Rojo',
            'activo' => true,
        ]);

        $valorMediano = AtributoValor::query()->create([
            'atributo_id' => $atributoTalla->id,
            'codigo' => 'M',
            'nombre' => 'Mediano',
            'activo' => true,
        ]);

        $generador = app(GeneradorClaveCombinacion::class);

        $primeraClave = $generador->generar(
            producto: $producto,
            valores: collect([$valorMediano, $valorRojo]),
        );

        $segundaClave = $generador->generar(
            producto: $producto,
            valores: collect([$valorRojo, $valorMediano]),
        );

        $this->assertSame(
            'COLOR=ROJO|TALLA=M',
            $primeraClave,
        );

        $this->assertSame($primeraClave, $segundaClave);
    }
}
