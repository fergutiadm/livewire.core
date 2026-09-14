<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Moneda;
use App\Models\Producto;
use App\Models\ProductoUnidad;
use App\Models\UnidadDimension;
use App\Models\UnidadMedida;
use App\Modules\Inventario\UnidadMedida\Exceptions\ConversionUnidadMedidaException;
use App\Modules\Inventario\UnidadMedida\Services\UnidadMedidaConversionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnidadMedidaConversionServiceTest extends TestCase
{
    use RefreshDatabase;

    private UnidadMedidaConversionService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(UnidadMedidaConversionService::class);
    }

    public function test_convierte_kg_a_gramos(): void
    {
        $dimension = UnidadDimension::create([
            'codigo' => 'masa',
            'nombre' => 'Masa',
            'activo' => true,
        ]);

        $kg = UnidadMedida::create([
            'unidad_dimension_id' => $dimension->id,
            'codigo' => 'kg',
            'nombre' => 'Kilogramo',
            'abreviatura' => 'kg',
            'tipo' => 'estandar',
            'factor_base' => '1',
            'es_base_dimension' => true,
            'activo' => true,
        ]);

        $g = UnidadMedida::create([
            'unidad_dimension_id' => $dimension->id,
            'codigo' => 'g',
            'nombre' => 'Gramo',
            'abreviatura' => 'g',
            'tipo' => 'estandar',
            'factor_base' => '0.001',
            'es_base_dimension' => false,
            'activo' => true,
        ]);

        $resultado = $this->service->convertir(
            '2.5',
            $kg,
            $g
        );

        $this->assertSame('2.5', $resultado->cantidadOriginal);
        $this->assertSame('2500.0000000000', $resultado->cantidadConvertida);
        $this->assertSame('1000.0000000000', $resultado->factorAplicado);
        $this->assertSame($kg->id, $resultado->unidadOrigen->id);
        $this->assertSame($g->id, $resultado->unidadDestino->id);
    }

    public function test_convierte_unidad_operacional_a_unidad_base(): void
    {
        $dimension = UnidadDimension::create([
            'codigo' => 'masa',
            'nombre' => 'Masa',
            'activo' => true,
        ]);

        $kg = UnidadMedida::create([
            'unidad_dimension_id' => $dimension->id,
            'codigo' => 'kg',
            'nombre' => 'Kilogramo',
            'abreviatura' => 'kg',
            'tipo' => 'estandar',
            'factor_base' => '1',
            'es_base_dimension' => true,
            'activo' => true,
        ]);

        $saco = UnidadMedida::create([
            'unidad_dimension_id' => null,
            'codigo' => 'saco',
            'nombre' => 'Saco',
            'abreviatura' => 'saco',
            'tipo' => 'operacional',
            'factor_base' => null,
            'es_base_dimension' => false,
            'activo' => true,
        ]);

        $categoria = Categoria::factory()->create();

        $moneda = Moneda::create([
    'codigo' => 'USD',
    'nombre' => 'Dólar estadounidense',
    'simbolo' => '$',
    'es_principal' => false,
    'tasa_cambio' => 1,
    'activa' => true,
]);

        $producto = Producto::factory()->create([
            'categoria_id' => $categoria->id,
            'moneda_id' => $moneda->id,
            'unidad_medida_base_id' => $kg->id,
        ]);

        ProductoUnidad::create([
            'producto_id' => $producto->id,
            'unidad_medida_id' => $saco->id,
            'factor_a_base' => '50',
            'activo' => true,
        ]);

        $resultado = $this->service->convertir(
            '10',
            $saco,
            $kg,
            $producto
        );

        $this->assertSame('500.0000000000', $resultado->cantidadConvertida);
    }

    public function test_convierte_unidad_base_a_unidad_operacional(): void
    {
        $dimension = UnidadDimension::create([
            'codigo' => 'masa',
            'nombre' => 'Masa',
            'activo' => true,
        ]);

        $kg = UnidadMedida::create([
            'unidad_dimension_id' => $dimension->id,
            'codigo' => 'kg',
            'nombre' => 'Kilogramo',
            'abreviatura' => 'kg',
            'tipo' => 'estandar',
            'factor_base' => '1',
            'es_base_dimension' => true,
            'activo' => true,
        ]);

        $saco = UnidadMedida::create([
            'unidad_dimension_id' => null,
            'codigo' => 'saco',
            'nombre' => 'Saco',
            'abreviatura' => 'saco',
            'tipo' => 'operacional',
            'factor_base' => null,
            'es_base_dimension' => false,
            'activo' => true,
        ]);

        $categoria = Categoria::factory()->create();

        $moneda = Moneda::create([
    'codigo' => 'USD',
    'nombre' => 'Dólar estadounidense',
    'simbolo' => '$',
    'es_principal' => false,
    'tasa_cambio' => 1,
    'activa' => true,
]);

        $producto = Producto::factory()->create([
            'categoria_id' => $categoria->id,
            'moneda_id' => $moneda->id,
            'unidad_medida_base_id' => $kg->id,
        ]);

        ProductoUnidad::create([
            'producto_id' => $producto->id,
            'unidad_medida_id' => $saco->id,
            'factor_a_base' => '50',
            'activo' => true,
        ]);

        $resultado = $this->service->convertir(
            '500',
            $kg,
            $saco,
            $producto
        );

        $this->assertSame('10.0000000000', $resultado->cantidadConvertida);
    }

    public function test_convierte_entre_dos_unidades_operacionales(): void
    {
        $dimension = UnidadDimension::create([
            'codigo' => 'cantidad',
            'nombre' => 'Cantidad',
            'activo' => true,
        ]);

        $unidad = UnidadMedida::create([
            'unidad_dimension_id' => $dimension->id,
            'codigo' => 'unidad',
            'nombre' => 'Unidad',
            'abreviatura' => 'ud',
            'tipo' => 'estandar',
            'factor_base' => '1',
            'es_base_dimension' => true,
            'activo' => true,
        ]);

        $caja = UnidadMedida::create([
            'unidad_dimension_id' => null,
            'codigo' => 'caja',
            'nombre' => 'Caja',
            'abreviatura' => 'caja',
            'tipo' => 'operacional',
            'factor_base' => null,
            'es_base_dimension' => false,
            'activo' => true,
        ]);

        $paquete = UnidadMedida::create([
            'unidad_dimension_id' => null,
            'codigo' => 'paquete',
            'nombre' => 'Paquete',
            'abreviatura' => 'paq',
            'tipo' => 'operacional',
            'factor_base' => null,
            'es_base_dimension' => false,
            'activo' => true,
        ]);

        $categoria = Categoria::factory()->create();

        $moneda = Moneda::create([
    'codigo' => 'USD',
    'nombre' => 'Dólar estadounidense',
    'simbolo' => '$',
    'es_principal' => false,
    'tasa_cambio' => 1,
    'activa' => true,
]);

        $producto = Producto::factory()->create([
            'categoria_id' => $categoria->id,
            'moneda_id' => $moneda->id,
            'unidad_medida_base_id' => $unidad->id,
        ]);

        ProductoUnidad::create([
            'producto_id' => $producto->id,
            'unidad_medida_id' => $caja->id,
            'factor_a_base' => '12',
            'activo' => true,
        ]);

        ProductoUnidad::create([
            'producto_id' => $producto->id,
            'unidad_medida_id' => $paquete->id,
            'factor_a_base' => '6',
            'activo' => true,
        ]);

        $resultado = $this->service->convertir(
            '2',
            $caja,
            $paquete,
            $producto
        );

        $this->assertSame('4.0000000000', $resultado->cantidadConvertida);
    }

    public function test_rechaza_conversion_entre_dimensiones_diferentes(): void
    {
        $masa = UnidadDimension::create([
            'codigo' => 'masa',
            'nombre' => 'Masa',
            'activo' => true,
        ]);

        $volumen = UnidadDimension::create([
            'codigo' => 'volumen',
            'nombre' => 'Volumen',
            'activo' => true,
        ]);

        $kg = UnidadMedida::create([
            'unidad_dimension_id' => $masa->id,
            'codigo' => 'kg',
            'nombre' => 'Kilogramo',
            'tipo' => 'estandar',
            'factor_base' => '1',
            'es_base_dimension' => true,
            'activo' => true,
        ]);

        $litro = UnidadMedida::create([
            'unidad_dimension_id' => $volumen->id,
            'codigo' => 'l',
            'nombre' => 'Litro',
            'tipo' => 'estandar',
            'factor_base' => '1',
            'es_base_dimension' => true,
            'activo' => true,
        ]);

        $this->expectException(ConversionUnidadMedidaException::class);

        $this->service->convertir(
            '1',
            $kg,
            $litro
        );
    }

    public function test_conversion_de_la_misma_unidad_no_requiere_producto(): void
    {
        $this->seedUnidadesMedida();
        $kg = UnidadMedida::where('codigo', 'kg')->firstOrFail();

        $resultado = $this->service->convertir(
            cantidad: '12.500',
            unidadOrigen: $kg,
            unidadDestino: $kg,
        );

        $this->assertSame('12.500', $resultado->cantidadOriginal);
        $this->assertSame('12.500', $resultado->cantidadConvertida);
        $this->assertSame('1', $resultado->factorAplicado);
        $this->assertSame($kg->id, $resultado->unidadOrigen->id);
        $this->assertSame($kg->id, $resultado->unidadDestino->id);
    }

    public function test_rechaza_unidad_operacional_sin_producto(): void
    {
        $this->seedUnidadesMedida();
        $saco = UnidadMedida::where('codigo', 'saco')->firstOrFail();
        $kg = UnidadMedida::where('codigo', 'kg')->firstOrFail();

        $this->expectException(ConversionUnidadMedidaException::class);
        $this->expectExceptionMessage(
            'Las conversiones que involucran unidades operativas requieren un producto.'
        );

        $this->service->convertir(
            cantidad: '2',
            unidadOrigen: $saco,
            unidadDestino: $kg,
        );
    }

    public function test_rechaza_unidad_operacional_no_configurada_para_el_producto(): void
    {
        $this->seedUnidadesMedida();
        $categoria = Categoria::factory()->create();

        $moneda = Moneda::create([
            'codigo' => 'USD',
            'nombre' => 'Dólar estadounidense',
            'simbolo' => '$',
            'es_principal' => true,
            'tasa_cambio' => 1,
            'activa' => true,
        ]);

        $kg = UnidadMedida::where('codigo', 'kg')->firstOrFail();
        $saco = UnidadMedida::where('codigo', 'saco')->firstOrFail();

        $producto = Producto::factory()->create([
            'categoria_id' => $categoria->id,
            'moneda_id' => $moneda->id,
            'unidad_medida_base_id' => $kg->id,
        ]);

        $this->expectException(ConversionUnidadMedidaException::class);
        $this->expectExceptionMessage(
            "La unidad {$saco->codigo} no está configurada para el producto {$producto->id}."
        );

        $this->service->convertir(
            cantidad: '2',
            unidadOrigen: $saco,
            unidadDestino: $kg,
            producto: $producto,
        );
    }

    public function test_rechaza_producto_sin_unidad_base(): void
    {
        $this->seedUnidadesMedida();
        $categoria = Categoria::factory()->create();

        $moneda = Moneda::create([
            'codigo' => 'USD',
            'nombre' => 'Dólar estadounidense',
            'simbolo' => '$',
            'es_principal' => true,
            'tasa_cambio' => 1,
            'activa' => true,
        ]);

        $saco = UnidadMedida::where('codigo', 'saco')->firstOrFail();
        $kg = UnidadMedida::where('codigo', 'kg')->firstOrFail();

        $producto = Producto::factory()->create([
            'categoria_id' => $categoria->id,
            'moneda_id' => $moneda->id,
            'unidad_medida_base_id' => null,
        ]);

        ProductoUnidad::create([
            'producto_id' => $producto->id,
            'unidad_medida_id' => $saco->id,
            'factor_a_base' => '50',
            'activo' => true,
        ]);

        $this->expectException(ConversionUnidadMedidaException::class);
        $this->expectExceptionMessage(
            "El producto {$producto->id} no tiene una unidad de medida base configurada."
        );

        $this->service->convertir(
            cantidad: '2',
            unidadOrigen: $saco,
            unidadDestino: $kg,
            producto: $producto,
        );
    }

    public function test_rechaza_producto_unidad_inactiva(): void
    {
        $this->seedUnidadesMedida();
        $categoria = Categoria::factory()->create();

        $moneda = Moneda::create([
            'codigo' => 'USD',
            'nombre' => 'Dólar estadounidense',
            'simbolo' => '$',
            'es_principal' => true,
            'tasa_cambio' => 1,
            'activa' => true,
        ]);

        $kg = UnidadMedida::where('codigo', 'kg')->firstOrFail();
        $saco = UnidadMedida::where('codigo', 'saco')->firstOrFail();

        $producto = Producto::factory()->create([
            'categoria_id' => $categoria->id,
            'moneda_id' => $moneda->id,
            'unidad_medida_base_id' => $kg->id,
        ]);

        ProductoUnidad::create([
            'producto_id' => $producto->id,
            'unidad_medida_id' => $saco->id,
            'factor_a_base' => '50',
            'activo' => false,
        ]);

        $this->expectException(ConversionUnidadMedidaException::class);
        $this->expectExceptionMessage(
            "La unidad {$saco->codigo} no está configurada para el producto {$producto->id}."
        );

        $this->service->convertir(
            cantidad: '2',
            unidadOrigen: $saco,
            unidadDestino: $kg,
            producto: $producto,
        );
    }

    public function test_rechaza_factor_operacional_menor_o_igual_a_cero(): void
    {
        $this->seedUnidadesMedida();
        $categoria = Categoria::factory()->create();

        $moneda = Moneda::create([
            'codigo' => 'USD',
            'nombre' => 'Dólar estadounidense',
            'simbolo' => '$',
            'es_principal' => true,
            'tasa_cambio' => 1,
            'activa' => true,
        ]);

        $kg = UnidadMedida::where('codigo', 'kg')->firstOrFail();
        $saco = UnidadMedida::where('codigo', 'saco')->firstOrFail();

        $producto = Producto::factory()->create([
            'categoria_id' => $categoria->id,
            'moneda_id' => $moneda->id,
            'unidad_medida_base_id' => $kg->id,
        ]);

        ProductoUnidad::create([
            'producto_id' => $producto->id,
            'unidad_medida_id' => $saco->id,
            'factor_a_base' => '0',
            'activo' => true,
        ]);

        $this->expectException(ConversionUnidadMedidaException::class);
        $this->expectExceptionMessage(
            "El factor de conversión de la unidad {$saco->codigo} debe ser mayor que cero."
        );

        $this->service->convertir(
            cantidad: '2',
            unidadOrigen: $saco,
            unidadDestino: $kg,
            producto: $producto,
        );
    }

    public function test_rechaza_cantidad_cero(): void
    {
        $this->seedUnidadesMedida();
        $kg = UnidadMedida::where('codigo', 'kg')->firstOrFail();
        $g = UnidadMedida::where('codigo', 'g')->firstOrFail();

        $this->expectException(ConversionUnidadMedidaException::class);
        $this->expectExceptionMessage(
            'La cantidad a convertir debe ser mayor que cero.'
        );

        $this->service->convertir(
            cantidad: '0',
            unidadOrigen: $kg,
            unidadDestino: $g,
        );
    }

    public function test_rechaza_cantidad_negativa(): void
    {
        $this->seedUnidadesMedida();
        $kg = UnidadMedida::where('codigo', 'kg')->firstOrFail();
        $g = UnidadMedida::where('codigo', 'g')->firstOrFail();

        $this->expectException(ConversionUnidadMedidaException::class);
        $this->expectExceptionMessage(
            'La cantidad debe ser un número decimal positivo válido.'
        );

        $this->service->convertir(
            cantidad: '-2',
            unidadOrigen: $kg,
            unidadDestino: $g,
        );
    }

    public function test_conversion_inversa_mantiene_la_cantidad_original(): void
    {
        $this->seedUnidadesMedida();
        $kg = UnidadMedida::where('codigo', 'kg')->firstOrFail();
        $g = UnidadMedida::where('codigo', 'g')->firstOrFail();

        $ida = $this->service->convertir(
            cantidad: '2.5',
            unidadOrigen: $kg,
            unidadDestino: $g,
        );

        $vuelta = $this->service->convertir(
            cantidad: $ida->cantidadConvertida,
            unidadOrigen: $g,
            unidadDestino: $kg,
        );

        $this->assertSame('2500.0000000000', $ida->cantidadConvertida);
        $this->assertSame('2.5000000000', $vuelta->cantidadConvertida);
    }

    public function test_conversion_entre_operacionales_utiliza_el_factor_de_cada_producto(): void
    {
        $this->seedUnidadesMedida();
        $categoria = Categoria::factory()->create();

        $moneda = Moneda::create([
            'codigo' => 'USD',
            'nombre' => 'Dólar estadounidense',
            'simbolo' => '$',
            'es_principal' => true,
            'tasa_cambio' => 1,
            'activa' => true,
        ]);

        $kg = UnidadMedida::where('codigo', 'kg')->firstOrFail();
        $saco = UnidadMedida::where('codigo', 'saco')->firstOrFail();
        $caja = UnidadMedida::where('codigo', 'caja')->firstOrFail();

        $producto = Producto::factory()->create([
            'categoria_id' => $categoria->id,
            'moneda_id' => $moneda->id,
            'unidad_medida_base_id' => $kg->id,
        ]);

        ProductoUnidad::create([
            'producto_id' => $producto->id,
            'unidad_medida_id' => $saco->id,
            'factor_a_base' => '50',
            'activo' => true,
        ]);

        ProductoUnidad::create([
            'producto_id' => $producto->id,
            'unidad_medida_id' => $caja->id,
            'factor_a_base' => '10',
            'activo' => true,
        ]);

        $resultado = $this->service->convertir(
            cantidad: '5',
            unidadOrigen: $saco,
            unidadDestino: $caja,
            producto: $producto,
        );

        $this->assertSame('25.0000000000', $resultado->cantidadConvertida);
        $this->assertSame('5.0000000000', $resultado->factorAplicado);
    }

    private function seedUnidadesMedida(): void
    {
        $this->seed(\Database\Seeders\UnidadMedidaSeeder::class);
    }
}