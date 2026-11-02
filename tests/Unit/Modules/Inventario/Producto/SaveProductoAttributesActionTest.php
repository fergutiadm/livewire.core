
<?php

namespace Tests\Unit\Modules\Inventario\Producto;

use App\Models\AtributableAtributo;
use App\Models\AtributableValor;
use App\Models\Atributo;
use App\Models\AtributoValor;
use App\Models\Categoria;
use App\Models\Local;
use App\Models\Moneda;
use App\Models\Producto;
use App\Models\ProductoVariante;
use App\Modules\Inventario\Producto\Actions\SaveProductoAttributesAction;
use App\Modules\Inventario\Producto\DTOs\SaveProductoAttributesDTO;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class SaveProductoAttributesActionTest extends TestCase
{
    use RefreshDatabase;

    private function crearProducto(): Producto
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

        return Producto::factory()->create([
            'local_id' => $local->id,
            'categoria_id' => $categoria->id,
            'moneda_id' => $moneda->id,
        ]);
    }

    private function crearAtributo(string $codigo): Atributo
    {
        return Atributo::query()->create([
            'codigo' => $codigo,
            'nombre' => $codigo,
            'activo' => true,
        ]);
    }

    private function crearValor(
        Atributo $atributo,
        string $codigo
    ): AtributoValor {
        return AtributoValor::query()->create([
            'atributo_id' => $atributo->id,
            'codigo' => $codigo,
            'nombre' => $codigo,
            'activo' => true,
        ]);
    }

    private function asignarAtributo(
        Producto $producto,
        Atributo $atributo
    ): void {
        AtributableAtributo::query()->create([
            'atributo_id' => $atributo->id,
            'atributable_type' => Producto::class,
            'atributable_id' => $producto->id,
            'orden_visual' => 1,
        ]);
    }

    private function asignarValor(
        Producto $producto,
        AtributoValor $valor
    ): void {
        AtributableValor::query()->create([
            'atributo_valor_id' => $valor->id,
            'atributable_type' => Producto::class,
            'atributable_id' => $producto->id,
            'orden_visual' => 1,
        ]);
    }

    private function crearVariante(
        Producto $producto,
        AtributoValor $valor
    ): ProductoVariante {
        $variante = ProductoVariante::query()->create([
            'producto_id' => $producto->id,
            'clave_combinacion' => $valor->codigo,
            'activo' => true,
        ]);

        $variante->atributosValores()->attach($valor->id);

        return $variante;
    }

    private function guardar(
        Producto $producto,
        array $cards
    ): void {
        app(SaveProductoAttributesAction::class)->execute(
            new SaveProductoAttributesDTO(
                productoId: $producto->id,
                cards: $cards,
            )
        );
    }

    public function test_impide_quitar_un_atributo_utilizado_por_una_variante(): void
    {
        $producto = $this->crearProducto();
        $atributo = $this->crearAtributo('COLOR');
        $valor = $this->crearValor($atributo, 'ROJO');

        $this->asignarAtributo($producto, $atributo);
        $this->asignarValor($producto, $valor);
        $this->crearVariante($producto, $valor);

        try {
            $this->guardar($producto, []);

            $this->fail('Debió impedir quitar el atributo.');
        } catch (ValidationException $e) {
            $this->assertDatabaseHas('atributables_atributos', [
                'atributo_id' => $atributo->id,
                'atributable_id' => $producto->id,
                'atributable_type' => Producto::class,
            ]);
        }
    }

    public function test_impide_quitar_un_valor_utilizado_por_una_variante(): void
    {
        $producto = $this->crearProducto();
        $atributo = $this->crearAtributo('COLOR');

        $valorRojo = $this->crearValor($atributo, 'ROJO');
        $valorAzul = $this->crearValor($atributo, 'AZUL');

        $this->asignarAtributo($producto, $atributo);
        $this->asignarValor($producto, $valorRojo);
        $this->asignarValor($producto, $valorAzul);

        $this->crearVariante($producto, $valorRojo);

        try {
            $this->guardar($producto, [
                $atributo->id => [
                    'atributo' => 'COLOR',
                    'orden_visual' => 1,
                    'valores' => [
                        $valorAzul->id => [
                            'valor' => 'AZUL',
                            'orden_visual' => 1,
                        ],
                    ],
                ],
            ]);

            $this->fail('Debió impedir quitar el valor utilizado.');
        } catch (ValidationException $e) {
            $this->assertDatabaseHas('atributables_valores', [
                'atributo_valor_id' => $valorRojo->id,
                'atributable_id' => $producto->id,
                'atributable_type' => Producto::class,
            ]);
        }
    }

    public function test_permite_quitar_un_valor_no_utilizado_por_variantes(): void
    {
        $producto = $this->crearProducto();
        $atributo = $this->crearAtributo('COLOR');

        $valorRojo = $this->crearValor($atributo, 'ROJO');
        $valorAzul = $this->crearValor($atributo, 'AZUL');

        $this->asignarAtributo($producto, $atributo);
        $this->asignarValor($producto, $valorRojo);
        $this->asignarValor($producto, $valorAzul);

        $this->guardar($producto, [
            $atributo->id => [
                'atributo' => 'COLOR',
                'orden_visual' => 1,
                'valores' => [
                    $valorAzul->id => [
                        'valor' => 'AZUL',
                        'orden_visual' => 1,
                    ],
                ],
            ],
        ]);

        $this->assertDatabaseMissing('atributables_valores', [
            'atributo_valor_id' => $valorRojo->id,
            'atributable_id' => $producto->id,
            'atributable_type' => Producto::class,
        ]);

        $this->assertDatabaseHas('atributables_valores', [
            'atributo_valor_id' => $valorAzul->id,
            'atributable_id' => $producto->id,
            'atributable_type' => Producto::class,
        ]);
    }


    public function test_permite_quitar_un_atributo_no_utilizado_por_variantes(): void
    {
        $producto = $this->crearProducto();
        $atributo = $this->crearAtributo('COLOR');
        $valor = $this->crearValor($atributo, 'ROJO');

        $this->asignarAtributo($producto, $atributo);
        $this->asignarValor($producto, $valor);

        $this->guardar($producto, []);

        $this->assertDatabaseMissing('atributables_atributos', [
            'atributo_id' => $atributo->id,
            'atributable_id' => $producto->id,
            'atributable_type' => Producto::class,
        ]);
    }
}
