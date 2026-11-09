<?php

namespace Tests\Unit\Modules\Finanzas\MonedaFondo;

use App\Models\ConceptoMonedaFondo;
use App\Models\Local;
use App\Models\Moneda;
use App\Models\MonedaFondo;
use App\Models\PeriodoContable;
use App\Models\TipoLocal;
use App\Models\User;
use App\Modules\Finanzas\MonedaFondo\Actions\CreateMonedaFondoAction;
use App\Modules\Finanzas\MonedaFondo\DTOs\CreateMonedaFondoDTO;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use Illuminate\Validation\ValidationException;

class CreateMonedaFondoActionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function puede_crear_un_movimiento_financiero_y_registrar_su_traza(): void
    {
        // Autenticamos antes de crear los modelos que generan trazas.
        $usuario = User::factory()->create();

        $this->actingAs($usuario);

        $tipoLocal = TipoLocal::query()
            ->where('codigo', 'operativo')
            ->firstOrFail();

        $local = Local::create([
            'nombre' => 'Local de prueba',
            'descripcion' => 'Local para pruebas de MonedaFondo',
            'tipo_local_id' => $tipoLocal->id,
        ]);

        $periodo = PeriodoContable::create([
            'local_id' => $local->id,
            'nombre' => 'Enero 2026',
            'fecha_inicio' => '2026-01-01',
            'fecha_fin' => '2026-01-31',
            'activo' => true,
            'cerrado' => false,
        ]);

        $moneda = Moneda::create([
            'codigo' => 'USD',
            'nombre' => 'Dólar estadounidense',
            'simbolo' => '$',
            'es_principal' => false,
            'tasa_cambio' => 450,
            'activa' => true,
        ]);

        $concepto = ConceptoMonedaFondo::create([
            'nombre' => 'Depósito manual',
            'tipo' => ConceptoMonedaFondo::TIPO_ESTANDAR,
            'slug' => 'deposito-manual',
            'entrada_salida' => true,
            'activo' => true,
        ]);

        $dto = CreateMonedaFondoDTO::fromArray([
            'local_id' => $local->id,
            'periodo_id' => $periodo->id,
            'moneda_id' => $moneda->id,
            'concepto_id' => $concepto->id,
            'descripcion' => 'Depósito de prueba',
            'monto' => '150.00',
            'tasa_cambio' => '450.0000000000',
            'fecha' => '2026-01-15',
        ]);

        $movimiento = app(CreateMonedaFondoAction::class)($dto);

        // Comprobamos el modelo devuelto.
        $this->assertInstanceOf(MonedaFondo::class, $movimiento);

        // Comprobamos la persistencia del movimiento.
        $this->assertDatabaseHas('moneda_fondos', [
            'id' => $movimiento->id,
            'local_id' => $local->id,
            'periodo_id' => $periodo->id,
            'moneda_id' => $moneda->id,
            'concepto_id' => $concepto->id,
            'generado_por_id' => $usuario->id,
            'descripcion' => 'Depósito de prueba',
            'monto' => '150.00',
            'fecha' => '2026-01-15',
        ]);

        // Comprobamos que el observer generó la traza correspondiente.
        $this->assertDatabaseHas('trazas', [
            'trazable_id' => $movimiento->id,
            'trazable_type' => MonedaFondo::class,
            'user_id' => $usuario->id,
            'operacion' => 'crear',
        ]);
    }


    /** @test */
    public function no_permite_crear_movimientos_en_un_periodo_contable_cerrado(): void
    {
        $usuario = User::factory()->create();

        $this->actingAs($usuario);

        $tipoLocal = TipoLocal::query()
            ->where('codigo', 'operativo')
            ->firstOrFail();

        $local = Local::create([
            'nombre' => 'Local de prueba',
            'descripcion' => 'Local para pruebas de MonedaFondo',
            'tipo_local_id' => $tipoLocal->id,
        ]);

        $periodo = PeriodoContable::create([
            'local_id' => $local->id,
            'nombre' => 'Enero 2026',
            'fecha_inicio' => '2026-01-01',
            'fecha_fin' => '2026-01-31',
            'activo' => false,
            'cerrado' => true,
        ]);

        $moneda = Moneda::create([
            'codigo' => 'USD',
            'nombre' => 'Dólar estadounidense',
            'simbolo' => '$',
            'es_principal' => false,
            'tasa_cambio' => 450,
            'activa' => true,
        ]);

        $concepto = ConceptoMonedaFondo::create([
            'nombre' => 'Depósito manual',
            'tipo' => ConceptoMonedaFondo::TIPO_ESTANDAR,
            'slug' => 'deposito-manual',
            'entrada_salida' => true,
            'activo' => true,
        ]);

        $dto = CreateMonedaFondoDTO::fromArray([
            'local_id' => $local->id,
            'periodo_id' => $periodo->id,
            'moneda_id' => $moneda->id,
            'concepto_id' => $concepto->id,
            'descripcion' => 'Depósito en período cerrado',
            'monto' => '150.00',
            'tasa_cambio' => '450.0000000000',
            'fecha' => '2026-01-15',
        ]);

        try {
            app(CreateMonedaFondoAction::class)($dto);

            $this->fail('Se esperaba una ValidationException por período cerrado.');
        } catch (\Illuminate\Validation\ValidationException $exception) {
            $this->assertArrayHasKey(
                'periodo_id',
                $exception->errors()
            );
        }

        $this->assertDatabaseCount('moneda_fondos', 0);
    }


    /** @test */
    public function no_permite_utilizar_un_periodo_contable_de_otro_local(): void
    {
        $usuario = User::factory()->create();

        $this->actingAs($usuario);

        $tipoLocal = TipoLocal::query()
            ->where('codigo', 'operativo')
            ->firstOrFail();

        $localA = Local::create([
            'nombre' => 'Local A',
            'descripcion' => 'Primer local de prueba',
            'tipo_local_id' => $tipoLocal->id,
        ]);

        $localB = Local::create([
            'nombre' => 'Local B',
            'descripcion' => 'Segundo local de prueba',
            'tipo_local_id' => $tipoLocal->id,
        ]);

        $periodoLocalB = PeriodoContable::create([
            'local_id' => $localB->id,
            'nombre' => 'Enero 2026',
            'fecha_inicio' => '2026-01-01',
            'fecha_fin' => '2026-01-31',
            'activo' => true,
            'cerrado' => false,
        ]);

        $moneda = Moneda::create([
            'codigo' => 'USD',
            'nombre' => 'Dólar estadounidense',
            'simbolo' => '$',
            'es_principal' => false,
            'tasa_cambio' => 450,
            'activa' => true,
        ]);

        $concepto = ConceptoMonedaFondo::create([
            'nombre' => 'Depósito manual',
            'tipo' => ConceptoMonedaFondo::TIPO_ESTANDAR,
            'slug' => 'deposito-manual',
            'entrada_salida' => true,
            'activo' => true,
        ]);

        $dto = CreateMonedaFondoDTO::fromArray([
            'local_id' => $localA->id,
            'periodo_id' => $periodoLocalB->id,
            'moneda_id' => $moneda->id,
            'concepto_id' => $concepto->id,
            'descripcion' => 'Movimiento con período de otro local',
            'monto' => '150.00',
            'tasa_cambio' => '450.0000000000',
            'fecha' => '2026-01-15',
        ]);

        try {
            app(CreateMonedaFondoAction::class)($dto);

            $this->fail('Se esperaba una ValidationException por incompatibilidad entre local y período.');
        } catch (\Illuminate\Validation\ValidationException $exception) {
            $this->assertArrayHasKey(
                'periodo_id',
                $exception->errors()
            );
        }

        $this->assertDatabaseCount('moneda_fondos', 0);
    }


    /** @test */
    public function no_permite_registrar_un_movimiento_fuera_de_las_fechas_del_periodo(): void
    {
        $usuario = User::factory()->create();

        $this->actingAs($usuario);

        $tipoLocal = TipoLocal::query()
            ->where('codigo', 'operativo')
            ->firstOrFail();

        $local = Local::create([
            'nombre' => 'Local de prueba',
            'descripcion' => 'Local para pruebas de MonedaFondo',
            'tipo_local_id' => $tipoLocal->id,
        ]);

        $periodo = PeriodoContable::create([
            'local_id' => $local->id,
            'nombre' => 'Enero 2026',
            'fecha_inicio' => '2026-01-01',
            'fecha_fin' => '2026-01-31',
            'activo' => true,
            'cerrado' => false,
        ]);

        $moneda = Moneda::create([
            'codigo' => 'USD',
            'nombre' => 'Dólar estadounidense',
            'simbolo' => '$',
            'es_principal' => false,
            'tasa_cambio' => 450,
            'activa' => true,
        ]);

        $concepto = ConceptoMonedaFondo::create([
            'nombre' => 'Depósito manual',
            'tipo' => ConceptoMonedaFondo::TIPO_ESTANDAR,
            'slug' => 'deposito-manual',
            'entrada_salida' => true,
            'activo' => true,
        ]);

        $dto = CreateMonedaFondoDTO::fromArray([
            'local_id' => $local->id,
            'periodo_id' => $periodo->id,
            'moneda_id' => $moneda->id,
            'concepto_id' => $concepto->id,
            'descripcion' => 'Movimiento fuera del período',
            'monto' => '150.00',
            'tasa_cambio' => '450.0000000000',
            'fecha' => '2026-02-01',
        ]);

        try {
            app(CreateMonedaFondoAction::class)($dto);

            $this->fail('Se esperaba una ValidationException por fecha fuera del período.');
        } catch (\Illuminate\Validation\ValidationException $exception) {
            $this->assertArrayHasKey(
                'fecha',
                $exception->errors()
            );
        }

        $this->assertDatabaseCount('moneda_fondos', 0);
    }


    /** @test */
    public function no_permite_crear_movimientos_sin_usuario_autenticado(): void
    {
        $contexto = $this->crearContextoBase(false);

        $dto = $this->crearDtoDesdeContexto($contexto);

        $this->assertRechazoDeAccion($dto, 'generado_por_id');
    }

    /** @test */
    public function no_permite_utilizar_un_periodo_inexistente(): void
    {
        $contexto = $this->crearContextoBase();

        $dto = $this->crearDtoDesdeContexto($contexto, [
            'periodo_id' => 999999,
        ]);

        $this->assertRechazoDeAccion($dto, 'periodo_id');
    }

    /** @test */
    public function no_permite_registrar_un_movimiento_antes_del_inicio_del_periodo(): void
    {
        $contexto = $this->crearContextoBase();

        $dto = $this->crearDtoDesdeContexto($contexto, [
            'fecha' => '2025-12-31',
        ]);

        $this->assertRechazoDeAccion($dto, 'fecha');
    }

    /** @test */
    public function no_permite_utilizar_una_moneda_inexistente(): void
    {
        $contexto = $this->crearContextoBase();

        $dto = $this->crearDtoDesdeContexto($contexto, [
            'moneda_id' => 999999,
        ]);

        $this->assertRechazoDeAccion($dto, 'moneda_id');
    }

    /** @test */
    public function no_permite_utilizar_una_moneda_inactiva(): void
    {
        $contexto = $this->crearContextoBase();

        $contexto['moneda']->update([
            'activa' => false,
        ]);

        $dto = $this->crearDtoDesdeContexto($contexto);

        $this->assertRechazoDeAccion($dto, 'moneda_id');
    }

    /** @test */
    public function no_permite_utilizar_un_concepto_inexistente(): void
    {
        $contexto = $this->crearContextoBase();

        $dto = $this->crearDtoDesdeContexto($contexto, [
            'concepto_id' => 999999,
        ]);

        $this->assertRechazoDeAccion($dto, 'concepto_id');
    }

    /** @test */
    public function no_permite_utilizar_un_concepto_inactivo(): void
    {
        $contexto = $this->crearContextoBase();

        $contexto['concepto']->update([
            'activo' => false,
        ]);

        $dto = $this->crearDtoDesdeContexto($contexto);

        $this->assertRechazoDeAccion($dto, 'concepto_id');
    }

    /** @test */
    public function permite_generar_movimientos_con_conceptos_operacionales(): void
    {
        $contexto = $this->crearContextoBase();

        $contexto['concepto']->update([
            'tipo' => ConceptoMonedaFondo::TIPO_OPERACIONAL,
        ]);

        $dto = $this->crearDtoDesdeContexto($contexto);

        $movimiento = app(CreateMonedaFondoAction::class)($dto);

        $this->assertInstanceOf(MonedaFondo::class, $movimiento);

        $this->assertDatabaseHas('moneda_fondos', [
            'id' => $movimiento->id,
            'concepto_id' => $contexto['concepto']->id,
            'generado_por_id' => $contexto['usuario']->id,
        ]);
    }

    /** @test */
    public function permite_registrar_movimientos_en_ambos_limites_del_periodo(): void
    {
        $contexto = $this->crearContextoBase();

        $dtoInicio = $this->crearDtoDesdeContexto($contexto, [
            'fecha' => '2026-01-01',
        ]);

        $dtoFin = $this->crearDtoDesdeContexto($contexto, [
            'fecha' => '2026-01-31',
        ]);

        $movimientoInicio = app(CreateMonedaFondoAction::class)($dtoInicio);
        $movimientoFin = app(CreateMonedaFondoAction::class)($dtoFin);

        $this->assertDatabaseHas('moneda_fondos', [
            'id' => $movimientoInicio->id,
            'fecha' => '2026-01-01',
        ]);

        $this->assertDatabaseHas('moneda_fondos', [
            'id' => $movimientoFin->id,
            'fecha' => '2026-01-31',
        ]);

        $this->assertDatabaseCount('moneda_fondos', 2);
    }

    /**
     * Prepara los registros necesarios para probar CreateMonedaFondoAction.
     */
    private function crearContextoBase(bool $autenticar = true): array
    {
        $usuario = User::factory()->create();

        if ($autenticar) {
            $this->actingAs($usuario);
        }

        $tipoLocal = TipoLocal::query()
            ->where('codigo', 'operativo')
            ->firstOrFail();

        $local = Local::create([
            'nombre' => 'Local de prueba',
            'descripcion' => 'Local para pruebas de MonedaFondo',
            'tipo_local_id' => $tipoLocal->id,
        ]);

        $periodo = PeriodoContable::create([
            'local_id' => $local->id,
            'nombre' => 'Enero 2026',
            'fecha_inicio' => '2026-01-01',
            'fecha_fin' => '2026-01-31',
            'activo' => true,
            'cerrado' => false,
        ]);

        $moneda = Moneda::create([
            'codigo' => 'USD',
            'nombre' => 'Dólar estadounidense',
            'simbolo' => '$',
            'es_principal' => false,
            'tasa_cambio' => 450,
            'activa' => true,
        ]);

        $concepto = ConceptoMonedaFondo::create([
            'nombre' => 'Depósito manual',
            'tipo' => ConceptoMonedaFondo::TIPO_ESTANDAR,
            'slug' => 'deposito-manual',
            'entrada_salida' => true,
            'activo' => true,
        ]);

        return compact(
            'usuario',
            'local',
            'periodo',
            'moneda',
            'concepto'
        );
    }

    /**
     * Construye un DTO válido y permite sobrescribir datos para cada escenario.
     */
    private function crearDtoDesdeContexto(
        array $contexto,
        array $sobrescrituras = []
    ): CreateMonedaFondoDTO {
        return CreateMonedaFondoDTO::fromArray(array_merge([
            'local_id' => $contexto['local']->id,
            'periodo_id' => $contexto['periodo']->id,
            'moneda_id' => $contexto['moneda']->id,
            'concepto_id' => $contexto['concepto']->id,
            'descripcion' => 'Movimiento de prueba',
            'monto' => '150.00',
            'tasa_cambio' => '450.0000000000',
            'fecha' => '2026-01-15',
        ], $sobrescrituras));
    }

    /**
     * Comprueba que la acción rechace los datos y no persista movimientos.
     */
    private function assertRechazoDeAccion(
        CreateMonedaFondoDTO $dto,
        string $campo
    ): void {
        try {
            app(CreateMonedaFondoAction::class)($dto);

            $this->fail('Se esperaba una ValidationException.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey(
                $campo,
                $exception->errors()
            );
        }

        $this->assertDatabaseCount('moneda_fondos', 0);
    }

}
