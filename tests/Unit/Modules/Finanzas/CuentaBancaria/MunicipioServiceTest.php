<?php

namespace Tests\Unit\Modules\Finanzas\CuentaBancaria;

use App\Models\Banco;
use App\Models\BancoSucursal;
use App\Models\Municipio;
use App\Models\Provincia;
use App\Modules\Configuracion\Municipio\Services\MunicipioService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MunicipioServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app(MunicipioService::class)->forgetCache();
    }

    protected function tearDown(): void
    {
        app(MunicipioService::class)->forgetCache();

        parent::tearDown();
    }

    public function test_por_provincia_retorna_sus_municipios(): void
    {
        $provincia = Provincia::create([
            'nombre' => 'Provincia Norte',
            'slug' => 'provincia-norte',
            'orden' => 1,
            'activa' => true,
        ]);

        $municipio = Municipio::create([
            'provincia_id' => $provincia->id,
            'nombre' => 'Municipio Norte',
            'slug' => 'municipio-norte',
            'activo' => true,
        ]);

        $resultado = app(MunicipioService::class)
            ->porProvincia($provincia->id);

        $this->assertCount(1, $resultado);
        $this->assertSame($municipio->id, $resultado[0]['id']);
        $this->assertSame($provincia->id, $resultado[0]['provincia_id']);
    }

    public function test_lista_solamente_municipios_activos(): void
    {
        $provincia = Provincia::create([
            'nombre' => 'Provincia Centro',
            'slug' => 'provincia-centro',
            'orden' => 1,
            'activa' => true,
        ]);

        Municipio::create([
            'provincia_id' => $provincia->id,
            'nombre' => 'Municipio Activo',
            'slug' => 'municipio-activo',
            'activo' => true,
        ]);

        Municipio::create([
            'provincia_id' => $provincia->id,
            'nombre' => 'Municipio Inactivo',
            'slug' => 'municipio-inactivo',
            'activo' => false,
        ]);

        $resultado = app(MunicipioService::class)->listForDisplay();

        $this->assertCount(1, $resultado);
        $this->assertSame('Municipio Activo', $resultado[0]['nombre']);
    }

    public function test_lista_disponibles_solo_municipios_con_sucursal_y_banco_activos(): void
    {
        $provincia = Provincia::create([
            'nombre' => 'Provincia Sur',
            'slug' => 'provincia-sur',
            'orden' => 1,
            'activa' => true,
        ]);

        $disponible = Municipio::create([
            'provincia_id' => $provincia->id,
            'nombre' => 'Municipio Disponible',
            'slug' => 'municipio-disponible-sur',
            'activo' => true,
        ]);

        $sinSucursalActiva = Municipio::create([
            'provincia_id' => $provincia->id,
            'nombre' => 'Municipio Sin Sucursal',
            'slug' => 'municipio-sin-sucursal-sur',
            'activo' => true,
        ]);

        $banco = Banco::create([
            'nombre' => 'Banco Sur',
            'codigo_swift' => 'BANCOSUR',
            'activo' => true,
        ]);

        BancoSucursal::create([
            'banco_id' => $banco->id,
            'municipio_id' => $disponible->id,
            'nombre' => 'Sucursal Disponible',
            'codigo' => 'SUR-ACTIVA',
            'activa' => true,
        ]);

        BancoSucursal::create([
            'banco_id' => $banco->id,
            'municipio_id' => $sinSucursalActiva->id,
            'nombre' => 'Sucursal Inactiva',
            'codigo' => 'SUR-INACTIVA',
            'activa' => false,
        ]);

        $resultado = app(MunicipioService::class)
            ->listForDisplayDisponibles();

        $ids = array_column($resultado, 'id');

        $this->assertContains($disponible->id, $ids);
        $this->assertNotContains($sinSucursalActiva->id, $ids);
    }
}
