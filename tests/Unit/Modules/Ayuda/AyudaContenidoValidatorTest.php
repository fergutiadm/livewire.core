<?php

namespace Tests\Unit\Modules\Ayuda;

use App\Modules\Ayuda\Services\AyudaContenidoValidator;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class AyudaContenidoValidatorTest extends TestCase
{
    private AyudaContenidoValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->validator = new AyudaContenidoValidator();
    }

    /** @test */
    public function permite_contenido_vacio_en_un_borrador(): void
    {
        $this->validator->validate('', requerido: false);

        $this->assertTrue(true);
    }

    /** @test */
    public function rechaza_contenido_vacio_cuando_es_obligatorio(): void
    {
        $this->assertValidationError('');
    }

    /** @test */
    public function rechaza_html_que_no_contiene_texto_real(): void
    {
        $this->assertValidationError('<p><br></p>');
        $this->assertValidationError('<p>&nbsp;</p>');
        $this->assertValidationError('<p>   </p>');
    }

    /** @test */
    public function acepta_exactamente_cincuenta_mil_caracteres(): void
    {
        $contenido = str_repeat('a', 50000);

        $this->validator->validate($contenido);

        $this->assertTrue(true);
    }

    /** @test */
    public function rechaza_mas_de_cincuenta_mil_caracteres(): void
    {
        $contenido = str_repeat('a', 50001);

        $this->assertValidationError($contenido);
    }

    /** @test */
    public function cuenta_correctamente_caracteres_utf8(): void
    {
        $contenido = str_repeat('á', 50000);

        $this->validator->validate($contenido);

        $this->assertTrue(true);
    }

    private function assertValidationError(string $contenido): void
    {
        try {
            $this->validator->validate($contenido);

            $this->fail('Se esperaba una ValidationException.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey(
                'contenido',
                $exception->errors()
            );
        }
    }
}