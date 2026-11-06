<?php

namespace Tests\Unit\Modules\Ayuda;

use App\Models\AyudaArticulo;
use App\Models\AyudaCategoria;
use App\Modules\Ayuda\AyudaArticulo\Actions\CreateAyudaArticuloAction;
use App\Modules\Ayuda\AyudaArticulo\Actions\ChangePublicationAyudaArticuloAction;
use App\Modules\Ayuda\AyudaArticulo\DTOs\CreateAyudaArticuloDTO;
use App\Modules\Ayuda\AyudaArticulo\DTOs\ChangePublicationAyudaArticuloDTO;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ChangePublicationAyudaArticuloActionTest extends TestCase
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
    public function publica_un_articulo_valido(): void
    {
        $articulo = $this->crearArticulo();

        $actualizado = $this->cambiarPublicacion($articulo, true);

        $this->assertTrue($actualizado->publicado);

        $this->assertDatabaseHas('ayuda_articulos', [
            'id' => $articulo->id,
            'publicado' => true,
        ]);
    }

    /** @test */
    public function rechaza_publicar_con_contenido_vacio(): void
    {
        $articulo = $this->crearArticulo([
            'contenido' => '',
        ]);

        $this->esperarError('contenido', $articulo);

        $this->assertDatabaseHas('ayuda_articulos', [
            'id' => $articulo->id,
            'publicado' => false,
        ]);
    }

    /** @test */
    public function rechaza_publicar_html_sin_texto_real(): void
    {
        $articulo = $this->crearArticulo([
            'contenido' => '<p><br></p><hr>',
        ]);

        $this->esperarError('contenido', $articulo);
    }

    /** @test */
    public function rechaza_publicar_si_la_categoria_esta_inactiva(): void
    {
        $articulo = $this->crearArticulo();

        $this->categoria->update(['activa' => false]);

        $this->esperarError('categoria_id', $articulo);

        $this->assertDatabaseHas('ayuda_articulos', [
            'id' => $articulo->id,
            'publicado' => false,
        ]);
    }

    /** @test */
    public function rechaza_publicar_si_el_titulo_esta_vacio(): void
    {
        $articulo = $this->crearArticulo();

        $articulo->update(['titulo' => '   ']);

        $this->esperarError('titulo', $articulo);
    }

    /** @test */
    public function permite_despublicar_con_categoria_inactiva(): void
    {
        $articulo = $this->crearArticulo();

        $articulo->update(['publicado' => true]);
        $this->categoria->update(['activa' => false]);

        $actualizado = $this->cambiarPublicacion($articulo, false);

        $this->assertFalse($actualizado->publicado);

        $this->assertDatabaseHas('ayuda_articulos', [
            'id' => $articulo->id,
            'publicado' => false,
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

    private function cambiarPublicacion(
        AyudaArticulo $articulo,
        bool $publicado
    ): AyudaArticulo {
        return app(ChangePublicationAyudaArticuloAction::class)(
            new ChangePublicationAyudaArticuloDTO(
                id: $articulo->id,
                publicado: $publicado,
            )
        );
    }

    private function esperarError(
        string $campo,
        AyudaArticulo $articulo
    ): void {
        try {
            $this->cambiarPublicacion($articulo, true);

            $this->fail('Se esperaba una ValidationException.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey(
                $campo,
                $exception->errors()
            );
        }
    }
}
