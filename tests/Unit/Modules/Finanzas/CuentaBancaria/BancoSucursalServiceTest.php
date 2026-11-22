<?php

namespace Tests\Unit\Modules\Finanzas\CuentaBancaria;

use App\Models\Banco;
use App\Models\BancoSucursal;
use App\Models\Municipio;
use App\Models\Provincia;
use App\Modules\Finanzas\BancoSucursal\Services\BancoSucursalService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BancoSucursalServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app(BancoSucursalService::class)->forgetCache();
    }

    protected function tearDown(): void
    {
        app(BancoSucursalService::class)->forgetCache();

        parent::tearDown();
    }

    public function test_lista_solamente_sucursales_activas(): void
    {
        [$banco, $municipio] = $this->crearUbicacion();

        $activa = BancoSucursal::create([
            'banco_id' => $banco->id,
            'municipio_id' => $municipio->id,
            'nombre' => 'Sucursal Activa',
            'codigo' => 'SUC-ACTIVA',
            'activa' => true,
        ]);

        BancoSucursal::create([
            'banco_id' => $banco->id,
            'municipio_id' => $municipio->id,
            'nombre' => 'Sucursal Inactiva',
            'codigo' => 'SUC-INACTIVA',
            'activa' => false,
        ]);

        $resultado = app(BancoSucursalService::class)->listForDisplay();

        $this->assertCount(1, $resultado);
        $this->assertSame($activa->id, $resultado[0]['id']);
        $this->assertSame('Sucursal Activa', $resultado[0]['nombre']);
    }

    public function test_por_banco_y_municipio_filtra_correctamente(): void
    {
        [$banco, $municipio] = $this->crearUbicacion();

        $otraProvincia = Provincia::create([
            'nombre' => 'Otra Provincia',
            'slug' => 'otra-provincia',
            'orden' => 2,
            'activa' => true,
        ]);

        $otroMunicipio = Municipio::create([
            'provincia_id' => $otraProvincia->id,
            'nombre' => 'Otro Municipio',
            'slug' => 'otro-municipio',
            'activo' => true,
        ]);

        $esperada = BancoSucursal::create([
            'banco_id' => $banco->id,
            'municipio_id' => $municipio->id,
            'nombre' => 'Sucursal Esperada',
            'codigo' => 'SUC-ESPERADA',
            'activa' => true,
        ]);

        BancoSucursal::create([
            'banco_id' => $banco->id,
            'municipio_id' => $otroMunicipio->id,
            'nombre' => 'Sucursal Otro Municipio',
            'codigo' => 'SUC-OTRO-MUNICIPIO',
            'activa' => true,
        ]);

        $resultado = app(BancoSucursalService::class)
            ->porBancoYMunicipio($banco->id, $municipio->id);

        $this->assertCount(1, $resultado);
        $this->assertSame($esperada->id, $resultado[0]['id']);
    }

    private function crearUbicacion(): array
    {
        $provincia = Provincia::create([
            'nombre' => 'Provincia de Prueba',
            'slug' => 'provincia-de-prueba',
            'orden' => 1,
            'activa' => true,
        ]);

        $municipio = Municipio::create([
            'provincia_id' => $provincia->id,
            'nombre' => 'Municipio de Prueba',
            'slug' => 'municipio-de-prueba',
            'activo' => true,
        ]);

        $banco = Banco::create([
            'nombre' => 'Banco de Prueba',
            'codigo_swift' => 'BANCOPRUEBA',
            'activo' => true,
        ]);

        return [$banco, $municipio];
    }
}
