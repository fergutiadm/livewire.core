<?php

namespace Tests\Unit\Modules\Ayuda;

use App\Models\AyudaArticulo;
use App\Models\AyudaCategoria;
use App\Modules\Ayuda\AyudaArticulo\Actions\CreateAyudaArticuloAction;
use App\Modules\Ayuda\AyudaArticulo\DTOs\CreateAyudaArticuloDTO;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CreateAyudaArticuloActionTest extends TestCase
{
    use RefreshDatabase;

    private AyudaCategoria $categoria;

    protected function setUp(): void
    {
        parent::setUp();

        $this->categoria = AyudaCategoria::create([
            'nombre' => 'Inventario',
            'slug' => 'inventario',
            'descripcion' => 'Ayuda sobre inventario',
            'orden_visual' => 1,
            'activa' => true,
        ]);
    }

    /** @test */
    public function crea_un_borrador_con_contenido_vacio(): void
    {
        $dto = $this->crearDTO([
            'contenido' => '',
        ]);

        $articulo = app(CreateAyudaArticuloAction::class)($dto);

        $this->assertInstanceOf(AyudaArticulo::class, $articulo);

        $this->assertFalse($articulo->publicado);

        $this->assertSame('', $articulo->contenido);

        $this->assertDatabaseHas('ayuda_articulos', [
            'id' => $articulo->id,
            'publicado' => false,
            'contenido' => '',
        ]);
    }

    /** @test */
    public function sanitiza_el_html_antes_de_persistir(): void
    {
        $dto = $this->crearDTO([
            'contenido' => '<p>Contenido seguro</p><script>alert("x")</script>',
        ]);

        $articulo = app(CreateAyudaArticuloAction::class)($dto);

        $this->assertStringContainsString(
            'Contenido seguro',
            $articulo->contenido
        );

        $this->assertStringNotContainsString(
            '<script',
            $articulo->contenido
        );

        $this->assertDatabaseHas('ayuda_articulos', [
            'id' => $articulo->id,
            'contenido' => $articulo->contenido,
        ]);
    }

    /** @test */
    public function rechaza_contenido_superior_a_cincuenta_mil_caracteres(): void
    {
        $dto = $this->crearDTO([
            'contenido' => str_repeat('a', 50001),
        ]);

        try {
            app(CreateAyudaArticuloAction::class)($dto);

            $this->fail('Se esperaba una ValidationException.');

        } catch (ValidationException $exception) {

            $this->assertArrayHasKey(
                'contenido',
                $exception->errors()
            );
        }

        $this->assertDatabaseCount('ayuda_articulos', 0);
    }

    /** @test */
    public function siempre_crea_el_articulo_como_borrador(): void
    {
        $dto = CreateAyudaArticuloDTO::fromArray([
            'categoria_id' => $this->categoria->id,
            'titulo' => 'Artículo de prueba',
            'slug' => 'articulo-de-prueba',
            'resumen' => null,
            'contenido' => '<p>Contenido</p>',
            'contexto' => null,
            'orden_visual' => 1,
            'publicado' => true,
        ]);

        $articulo = app(CreateAyudaArticuloAction::class)($dto);

        $this->assertFalse($articulo->publicado);
    }

    private function crearDTO(array $sobrescritura = []): CreateAyudaArticuloDTO
    {
        return CreateAyudaArticuloDTO::fromArray(array_merge([
            'categoria_id' => $this->categoria->id,
            'titulo' => 'Artículo de prueba',
            'slug' => 'articulo-de-prueba',
            'resumen' => null,
            'contenido' => '<p>Contenido de prueba</p>',
            'contexto' => null,
            'orden_visual' => 1,
        ], $sobrescritura));
    }
}