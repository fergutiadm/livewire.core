<?php

namespace Tests\Unit\Modules\Ayuda;

use App\Models\AyudaArticulo;
use App\Models\AyudaCategoria;
use App\Modules\Ayuda\AyudaArticulo\Actions\CreateAyudaArticuloAction;
use App\Modules\Ayuda\AyudaArticulo\Actions\UpdateAyudaArticuloAction;
use App\Modules\Ayuda\AyudaArticulo\DTOs\CreateAyudaArticuloDTO;
use App\Modules\Ayuda\AyudaArticulo\DTOs\UpdateAyudaArticuloDTO;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class UpdateAyudaArticuloActionTest extends TestCase
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
    public function permite_actualizar_un_borrador_con_contenido_vacio(): void
    {
        $articulo = $this->crearArticulo();

        $actualizado = $this->actualizarArticulo($articulo, [
            'contenido' => '',
        ]);

        $this->assertSame('', $actualizado->contenido);
        $this->assertFalse($actualizado->publicado);
    }

    /** @test */
    public function sanitiza_el_html_antes_de_persistir(): void
    {
        $articulo = $this->crearArticulo();

        $actualizado = $this->actualizarArticulo($articulo, [
            'contenido' => '<p>Contenido actualizado</p><script>alert("x")</script>',
        ]);

        $this->assertStringContainsString(
            'Contenido actualizado',
            $actualizado->contenido
        );

        $this->assertStringNotContainsString(
            '<script',
            $actualizado->contenido
        );

        $this->assertDatabaseHas('ayuda_articulos', [
            'id' => $articulo->id,
            'contenido' => $actualizado->contenido,
        ]);
    }

    /** @test */
    public function no_permite_dejar_sin_contenido_un_articulo_publicado(): void
    {
        $articulo = $this->crearArticulo([
            'contenido' => '<p>Contenido publicado</p>',
        ]);

        $articulo->update(['publicado' => true]);

        try {
            $this->actualizarArticulo($articulo, [
                'contenido' => '',
            ]);

            $this->fail('Se esperaba una ValidationException.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('contenido', $exception->errors());
        }

        $this->assertDatabaseHas('ayuda_articulos', [
            'id' => $articulo->id,
            'publicado' => true,
            'contenido' => '<p>Contenido publicado</p>',
        ]);
    }

    /** @test */
    public function actualizar_no_modifica_el_estado_de_publicacion(): void
    {
        $articulo = $this->crearArticulo([
            'contenido' => '<p>Contenido publicado</p>',
        ]);

        $articulo->update(['publicado' => true]);

        $actualizado = $this->actualizarArticulo($articulo, [
            'titulo' => 'Título actualizado',
        ]);

        $this->assertSame('Título actualizado', $actualizado->titulo);
        $this->assertTrue($actualizado->publicado);
    }

    /** @test */
    public function articulo_publicado_no_puede_usar_una_categoria_inactiva(): void
    {
        $articulo = $this->crearArticulo([
            'contenido' => '<p>Contenido publicado</p>',
        ]);

        $articulo->update(['publicado' => true]);

        $categoriaInactiva = AyudaCategoria::create([
            'nombre' => 'Categoría inactiva',
            'slug' => 'categoria-inactiva',
            'descripcion' => null,
            'orden_visual' => 2,
            'activa' => false,
        ]);

        try {
            $this->actualizarArticulo($articulo, [
                'categoria_id' => $categoriaInactiva->id,
            ]);

            $this->fail('Se esperaba una ValidationException.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('categoria_id', $exception->errors());
        }

        $this->assertDatabaseHas('ayuda_articulos', [
            'id' => $articulo->id,
            'categoria_id' => $this->categoria->id,
            'publicado' => true,
        ]);
    }

    private function crearArticulo(array $sobrescritura = []): AyudaArticulo
    {
        $data = array_merge([
            'categoria_id' => $this->categoria->id,
            'titulo' => 'Artículo de prueba',
            'slug' => 'articulo-de-prueba',
            'resumen' => null,
            'contenido' => '<p>Contenido de prueba</p>',
            'contexto' => null,
            'orden_visual' => 1,
        ], $sobrescritura);

        return app(CreateAyudaArticuloAction::class)(
            CreateAyudaArticuloDTO::fromArray($data)
        );
    }

    private function actualizarArticulo(
        AyudaArticulo $articulo,
        array $sobrescritura = []
    ): AyudaArticulo {
        $data = array_merge([
            'id' => $articulo->id,
            'categoria_id' => $this->categoria->id,
            'titulo' => $articulo->titulo,
            'slug' => $articulo->slug,
            'resumen' => $articulo->resumen,
            'contenido' => $articulo->contenido,
            'contexto' => $articulo->contexto,
            'orden_visual' => $articulo->orden_visual,
        ], $sobrescritura);

        return app(UpdateAyudaArticuloAction::class)(
            UpdateAyudaArticuloDTO::fromArray($data)
        );
    }
}
