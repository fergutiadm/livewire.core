<?php

namespace Tests\Unit\Modules\Finanzas\CuentaBancaria;

use App\Models\Banco;
use App\Models\BancoSucursal;
use App\Models\Municipio;
use App\Models\Provincia;
use App\Modules\Configuracion\Provincia\Services\ProvinciaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProvinciaServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app(ProvinciaService::class)->forgetCache();
    }

    protected function tearDown(): void
    {
        app(ProvinciaService::class)->forgetCache();

        parent::tearDown();
    }

    public function test_lista_solamente_provincias_activas(): void
    {
        $activa = Provincia::create([
            'nombre' => 'Provincia Activa',
            'slug' => 'provincia-activa',
            'orden' => 1,
            'activa' => true,
        ]);

        Provincia::create([
            'nombre' => 'Provincia Inactiva',
            'slug' => 'provincia-inactiva',
            'orden' => 2,
            'activa' => false,
        ]);

        $resultado = app(ProvinciaService::class)->listForDisplay();

        $this->assertCount(1, $resultado);
        $this->assertSame($activa->id, $resultado[0]['id']);
        $this->assertSame('Provincia Activa', $resultado[0]['nombre']);
    }

    public function test_lista_disponibles_solo_provincias_con_sucursales_activas_y_bancos_activos(): void
    {
        $disponible = Provincia::create([
            'nombre' => 'Provincia Disponible',
            'slug' => 'provincia-disponible',
            'orden' => 1,
            'activa' => true,
        ]);

        $sinSucursalDisponible = Provincia::create([
            'nombre' => 'Provincia Sin Sucursal',
            'slug' => 'provincia-sin-sucursal',
            'orden' => 2,
            'activa' => true,
        ]);

        $municipioDisponible = Municipio::create([
            'provincia_id' => $disponible->id,
            'nombre' => 'Municipio Disponible',
            'slug' => 'municipio-disponible',
            'activo' => true,
        ]);

        $municipioNoDisponible = Municipio::create([
            'provincia_id' => $sinSucursalDisponible->id,
            'nombre' => 'Municipio Sin Sucursal',
            'slug' => 'municipio-sin-sucursal',
            'activo' => true,
        ]);

        $banco = Banco::create([
            'nombre' => 'Banco Activo',
            'codigo_swift' => 'BANCACTIVO',
            'activo' => true,
        ]);

        BancoSucursal::create([
            'banco_id' => $banco->id,
            'municipio_id' => $municipioDisponible->id,
            'nombre' => 'Sucursal Activa',
            'codigo' => 'SUC-ACTIVA',
            'activa' => true,
        ]);

        BancoSucursal::create([
            'banco_id' => $banco->id,
            'municipio_id' => $municipioNoDisponible->id,
            'nombre' => 'Sucursal Inactiva',
            'codigo' => 'SUC-INACTIVA',
            'activa' => false,
        ]);

        $resultado = app(ProvinciaService::class)
            ->listForDisplayDisponibles();

        $ids = array_column($resultado, 'id');

        $this->assertContains($disponible->id, $ids);
        $this->assertNotContains($sinSucursalDisponible->id, $ids);
    }
}
