<?php

namespace Tests\Unit\Modules\Finanzas\MonedaFondo;

use App\Core\CQRS\ActionRegistry;
use App\Modules\Finanzas\MonedaFondo\DTOs\CreateMonedaFondoDTO;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class MonedaFondoCqrsTest extends TestCase
{
    /** @test */
    public function resuelve_automaticamente_la_accion_y_su_dto(): void
    {
        $action = app(ActionRegistry::class)
            ->resolve('monedaFondo.create');

        $this->assertSame(
            'App\\Modules\\Finanzas\\MonedaFondo\\Actions\\CreateMonedaFondoAction',
            $action
        );

        $dto = str_replace('\\Actions\\', '\\DTOs\\', $action);
        $dto = str_replace('Action', 'DTO', $dto);

        $this->assertSame(
            CreateMonedaFondoDTO::class,
            $dto
        );

        $this->assertTrue(class_exists($dto));
    }

    /** @test */
    public function construye_el_dto_con_datos_validos(): void
    {
        $dto = CreateMonedaFondoDTO::fromArray([
            'local_id' => 1,
            'periodo_id' => 1,
            'moneda_id' => 1,
            'concepto_id' => 1,
            'descripcion' => '  Depósito manual  ',
            'monto' => '150.50',
            'tasa_cambio' => '1',
            'fecha' => '2026-11-09',
        ]);

        $this->assertInstanceOf(CreateMonedaFondoDTO::class, $dto);
        $this->assertSame('Depósito manual', $dto->descripcion);
        $this->assertSame('150.50', $dto->monto);
        $this->assertSame('1', $dto->tasa_cambio);
        $this->assertSame(1, $dto->local_id);
    }

    /** @test */
    public function rechaza_un_monto_con_mas_de_dos_decimales(): void
    {
        $this->expectException(ValidationException::class);

        CreateMonedaFondoDTO::fromArray([
            'local_id' => 1,
            'periodo_id' => 1,
            'moneda_id' => 1,
            'concepto_id' => 1,
            'descripcion' => 'Depósito manual',
            'monto' => '150.555',
            'tasa_cambio' => '1',
            'fecha' => '2026-11-09',
        ]);
    }

    /** @test */
    public function rechaza_una_tasa_de_cambio_cero(): void
    {
        $this->expectException(ValidationException::class);

        CreateMonedaFondoDTO::fromArray([
            'local_id' => 1,
            'periodo_id' => 1,
            'moneda_id' => 1,
            'concepto_id' => 1,
            'descripcion' => 'Depósito manual',
            'monto' => '150.00',
            'tasa_cambio' => '0',
            'fecha' => '2026-11-09',
        ]);
    }

    /** @test */
    public function rechaza_un_monto_igual_a_cero(): void
    {
        $this->expectException(ValidationException::class);

        CreateMonedaFondoDTO::fromArray([
            'local_id' => 1,
            'periodo_id' => 1,
            'moneda_id' => 1,
            'concepto_id' => 1,
            'descripcion' => 'Depósito manual',
            'monto' => '0',
            'tasa_cambio' => '1',
            'fecha' => '2026-11-09',
        ]);
    }

    /** @test */
    public function rechaza_un_monto_negativo(): void
    {
        $this->expectException(ValidationException::class);

        CreateMonedaFondoDTO::fromArray([
            'local_id' => 1,
            'periodo_id' => 1,
            'moneda_id' => 1,
            'concepto_id' => 1,
            'descripcion' => 'Depósito manual',
            'monto' => '-10',
            'tasa_cambio' => '1',
            'fecha' => '2026-11-09',
        ]);
    }

    /** @test */
    public function rechaza_una_tasa_con_mas_de_diez_decimales(): void
    {
        $this->expectException(ValidationException::class);

        CreateMonedaFondoDTO::fromArray([
            'local_id' => 1,
            'periodo_id' => 1,
            'moneda_id' => 1,
            'concepto_id' => 1,
            'descripcion' => 'Depósito manual',
            'monto' => '100',
            'tasa_cambio' => '1.12345678901',
            'fecha' => '2026-11-09',
        ]);
    }

    /** @test */
    public function rechaza_una_descripcion_vacia(): void
    {
        $this->expectException(ValidationException::class);

        CreateMonedaFondoDTO::fromArray([
            'local_id' => 1,
            'periodo_id' => 1,
            'moneda_id' => 1,
            'concepto_id' => 1,
            'descripcion' => '   ',
            'monto' => '100',
            'tasa_cambio' => '1',
            'fecha' => '2026-11-09',
        ]);
    }

    /** @test */
    public function rechaza_una_fecha_con_formato_incorrecto(): void
    {
        $this->expectException(ValidationException::class);

        CreateMonedaFondoDTO::fromArray([
            'local_id' => 1,
            'periodo_id' => 1,
            'moneda_id' => 1,
            'concepto_id' => 1,
            'descripcion' => 'Depósito manual',
            'monto' => '100',
            'tasa_cambio' => '1',
            'fecha' => '09/11/2026',
        ]);
    }

    /** @test */
    public function rechaza_un_identificador_de_local_no_valido(): void
    {
        $this->expectException(ValidationException::class);

        CreateMonedaFondoDTO::fromArray([
            'local_id' => 0,
            'periodo_id' => 1,
            'moneda_id' => 1,
            'concepto_id' => 1,
            'descripcion' => 'Depósito manual',
            'monto' => '100',
            'tasa_cambio' => '1',
            'fecha' => '2026-11-09',
        ]);
    }

        /** @test */
    public function rechaza_un_monto_que_supera_los_digitos_permitidos(): void
    {
        $this->expectException(ValidationException::class);

        CreateMonedaFondoDTO::fromArray([
            'local_id' => 1,
            'periodo_id' => 1,
            'moneda_id' => 1,
            'concepto_id' => 1,
            'descripcion' => 'Depósito manual',
            'monto' => '1000000000000000000',
            'tasa_cambio' => '1',
            'fecha' => '2026-11-09',
        ]);
    }

    /** @test */
    public function rechaza_una_tasa_que_supera_los_digitos_enteros_permitidos(): void
    {
        $this->expectException(ValidationException::class);

        CreateMonedaFondoDTO::fromArray([
            'local_id' => 1,
            'periodo_id' => 1,
            'moneda_id' => 1,
            'concepto_id' => 1,
            'descripcion' => 'Depósito manual',
            'monto' => '100',
            'tasa_cambio' => '12345678901',
            'fecha' => '2026-11-09',
        ]);
    }

    /** @test */
    public function rechaza_una_fecha_calendariamente_invalida(): void
    {
        $this->expectException(ValidationException::class);

        CreateMonedaFondoDTO::fromArray([
            'local_id' => 1,
            'periodo_id' => 1,
            'moneda_id' => 1,
            'concepto_id' => 1,
            'descripcion' => 'Depósito manual',
            'monto' => '100',
            'tasa_cambio' => '1',
            'fecha' => '2026-02-30',
        ]);
    }

    /** @test */
    public function rechaza_un_identificador_que_no_es_entero(): void
    {
        $this->expectException(ValidationException::class);

        CreateMonedaFondoDTO::fromArray([
            'local_id' => 'abc',
            'periodo_id' => 1,
            'moneda_id' => 1,
            'concepto_id' => 1,
            'descripcion' => 'Depósito manual',
            'monto' => '100',
            'tasa_cambio' => '1',
            'fecha' => '2026-11-09',
        ]);
    }

    /** @test */
    public function rechaza_una_descripcion_mayor_de_255_caracteres(): void
    {
        $this->expectException(ValidationException::class);

        CreateMonedaFondoDTO::fromArray([
            'local_id' => 1,
            'periodo_id' => 1,
            'moneda_id' => 1,
            'concepto_id' => 1,
            'descripcion' => str_repeat('A', 256),
            'monto' => '100',
            'tasa_cambio' => '1',
            'fecha' => '2026-11-09',
        ]);
    }

    /** @test */
    public function rechaza_un_concepto_que_no_es_entero(): void
    {
        $this->expectException(ValidationException::class);

        CreateMonedaFondoDTO::fromArray([
            'local_id' => 1,
            'periodo_id' => 1,
            'moneda_id' => 1,
            'concepto_id' => 'concepto',
            'descripcion' => 'Depósito manual',
            'monto' => '100',
            'tasa_cambio' => '1',
            'fecha' => '2026-11-09',
        ]);
    }
}