<?php

namespace Tests\Unit\Modules\Inventario\ProductoVariante;

use App\Models\AtributableAtributo;
use App\Models\AtributableValor;
use App\Models\Atributo;
use App\Models\AtributoValor;
use App\Models\Categoria;
use App\Models\Local;
use App\Models\Moneda;
use App\Models\Producto;
use App\Models\ProductoVariante;
use App\Modules\Inventario\ProductoVariante\Actions\CreateProductoVarianteAction;
use App\Modules\Inventario\ProductoVariante\Actions\UpdateProductoVarianteAction;
use App\Modules\Inventario\ProductoVariante\DTOs\CreateProductoVarianteDTO;
use App\Modules\Inventario\ProductoVariante\DTOs\UpdateProductoVarianteDTO;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ProductoVarianteActionsTest extends TestCase
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
            'activo' => true,
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
            'activo' => true,
        ]);
    }

    private function crearVariante(
        Producto $producto,
        AtributoValor $valor,
        ?string $nombre = null
    ): ProductoVariante {
        return app(CreateProductoVarianteAction::class)(
            new CreateProductoVarianteDTO(
                productoId: $producto->id,
                atributosValores: [
                    ['atributo_valor_id' => $valor->id],
                ],
                nombre: $nombre,
            )
        );
    }

    private function actualizarVariante(
        ProductoVariante $variante,
        array $valores,
        ?string $nombre = null,
        bool $activo = true
    ): ProductoVariante {
        return app(UpdateProductoVarianteAction::class)(
            new UpdateProductoVarianteDTO(
                id: $variante->id,
                productoId: $variante->producto_id,
                atributosValores: $valores,
                nombre: $nombre,
                activo: $activo,
            )
        );
    }

    public function test_permite_actualizar_nombre_de_variante_con_valor_inactivo(): void
    {
        $producto = $this->crearProducto();
        $atributo = $this->crearAtributo('COLOR');
        $valor = $this->crearValor($atributo, 'ROJO');

        $this->asignarAtributo($producto, $atributo);
        $this->asignarValor($producto, $valor);

        $variante = $this->crearVariante(
            $producto,
            $valor,
            'Producto rojo'
        );

        $valor->update(['activo' => false]);

        $actualizada = $this->actualizarVariante(
            $variante,
            [
                ['atributo_valor_id' => $valor->id],
            ],
            'Producto rojo actualizado'
        );

        $this->assertSame(
            'Producto rojo actualizado',
            $actualizada->nombre
        );

        $this->assertSame(
            $variante->clave_combinacion,
            $actualizada->clave_combinacion
        );

        $this->assertDatabaseHas(
            'producto_variantes_atributos_valores',
            [
                'producto_variante_id' => $variante->id,
                'atributo_valor_id' => $valor->id,
            ]
        );
    }

    public function test_impide_cambiar_variante_a_una_combinacion_con_valor_inactivo(): void
    {
        $producto = $this->crearProducto();
        $atributo = $this->crearAtributo('COLOR');

        $valorRojo = $this->crearValor($atributo, 'ROJO');
        $valorAzul = $this->crearValor($atributo, 'AZUL');

        $this->asignarAtributo($producto, $atributo);
        $this->asignarValor($producto, $valorRojo);
        $this->asignarValor($producto, $valorAzul);

        $variante = $this->crearVariante(
            $producto,
            $valorRojo,
            'Producto rojo'
        );

        $valorAzul->update(['activo' => false]);

        try {
            $this->actualizarVariante(
                $variante,
                [
                    ['atributo_valor_id' => $valorAzul->id],
                ],
                'Producto azul'
            );

            $this->fail(
                'Debió impedir cambiar a una combinación con valor inactivo.'
            );
        } catch (ValidationException $e) {
            $this->assertDatabaseHas('producto_variantes', [
                'id' => $variante->id,
                'nombre' => 'Producto rojo',
            ]);

            $this->assertDatabaseHas(
                'producto_variantes_atributos_valores',
                [
                    'producto_variante_id' => $variante->id,
                    'atributo_valor_id' => $valorRojo->id,
                ]
            );

            $this->assertDatabaseMissing(
                'producto_variantes_atributos_valores',
                [
                    'producto_variante_id' => $variante->id,
                    'atributo_valor_id' => $valorAzul->id,
                ]
            );
        }
    }

    public function test_impide_crear_variante_con_valor_inactivo(): void
    {
        $producto = $this->crearProducto();
        $atributo = $this->crearAtributo('COLOR');
        $valor = $this->crearValor($atributo, 'ROJO');

        $this->asignarAtributo($producto, $atributo);
        $this->asignarValor($producto, $valor);

        $valor->update(['activo' => false]);

        try {
            $this->crearVariante($producto, $valor);

            $this->fail(
                'Debió impedir crear una variante con un valor inactivo.'
            );
        } catch (ValidationException $e) {
            $this->assertDatabaseMissing('producto_variantes', [
                'producto_id' => $producto->id,
            ]);
        }
    }

    public function test_impide_crear_variante_con_atributo_inactivo(): void
    {
        $producto = $this->crearProducto();
        $atributo = $this->crearAtributo('COLOR');
        $valor = $this->crearValor($atributo, 'ROJO');

        $this->asignarAtributo($producto, $atributo);
        $this->asignarValor($producto, $valor);

        $atributo->update(['activo' => false]);

        try {
            $this->crearVariante($producto, $valor);

            $this->fail(
                'Debió impedir crear una variante con un atributo inactivo.'
            );
        } catch (ValidationException $e) {
            $this->assertDatabaseMissing('producto_variantes', [
                'producto_id' => $producto->id,
            ]);
        }
    }
}
