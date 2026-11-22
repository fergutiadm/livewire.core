<?php

namespace Tests\Unit\Modules\Finanzas\CuentaBancaria;

use App\Models\Banco;
use App\Modules\Finanzas\Banco\Services\BancoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BancoServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app(BancoService::class)->forgetCache();
    }

    protected function tearDown(): void
    {
        app(BancoService::class)->forgetCache();

        parent::tearDown();
    }

    public function test_lista_solamente_bancos_activos(): void
    {
        $activo = Banco::create([
            'nombre' => 'Banco Activo',
            'codigo_swift' => 'BANCOACTIVO',
            'activo' => true,
        ]);

        Banco::create([
            'nombre' => 'Banco Inactivo',
            'codigo_swift' => 'BANCOINACTIVO',
            'activo' => false,
        ]);

        $resultado = app(BancoService::class)->listForDisplay();

        $this->assertCount(1, $resultado);
        $this->assertSame($activo->id, $resultado[0]['id']);
        $this->assertSame('Banco Activo', $resultado[0]['nombre']);
    }

    public function test_get_permite_proyectar_los_campos_solicitados(): void
    {
        Banco::create([
            'nombre' => 'Banco de Prueba',
            'alias' => 'BDP',
            'codigo_swift' => 'BANCOPRUEBA',
            'activo' => true,
        ]);

        $resultado = app(BancoService::class)
            ->get(['id', 'nombre']);

        $this->assertCount(1, $resultado);
        $this->assertSame(
            ['id', 'nombre'],
            array_keys($resultado[0])
        );
        $this->assertSame('Banco de Prueba', $resultado[0]['nombre']);
    }
}
