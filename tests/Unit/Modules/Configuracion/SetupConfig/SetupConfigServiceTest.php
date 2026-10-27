<?php

namespace Tests\Unit\Modules\Configuracion\SetupConfig;

use App\Models\SetupConfig;
use App\Modules\Configuracion\SetupConfig\Actions\CreateSetupConfigAction;
use App\Modules\Configuracion\SetupConfig\Actions\UpdateSetupConfigAction;
use App\Modules\Configuracion\SetupConfig\Actions\DeleteSetupConfigAction;
use App\Modules\Configuracion\SetupConfig\DTOs\CreateSetupConfigDTO;
use App\Modules\Configuracion\SetupConfig\DTOs\UpdateSetupConfigDTO;
use App\Modules\Configuracion\SetupConfig\DTOs\DeleteSetupConfigDTO;
use App\Modules\Configuracion\SetupConfig\Services\SetupConfigService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SetupConfigServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app(SetupConfigService::class)->forgetCache();
    }

    protected function tearDown(): void
    {
        try {
            app(SetupConfigService::class)->forgetCache();
        } finally {
            parent::tearDown();
        }
    }

    public function test_convierte_valores_segun_su_tipo(): void
    {
        SetupConfig::create([
            'nombre' => 'Cantidad',
            'item_id' => 'cantidad_demo',
            'tipo' => SetupConfig::TIPO_NUMERO,
            'valor' => '15',
        ]);

        SetupConfig::create([
            'nombre' => 'Tasa',
            'item_id' => 'tasa_demo',
            'tipo' => SetupConfig::TIPO_NUMERO,
            'valor' => '350.25',
        ]);

        SetupConfig::create([
            'nombre' => 'Empresa',
            'item_id' => 'empresa_demo',
            'tipo' => SetupConfig::TIPO_TEXTO,
            'valor' => 'Empresa de prueba',
        ]);

        SetupConfig::create([
            'nombre' => 'Monedas',
            'item_id' => 'monedas_demo',
            'tipo' => SetupConfig::TIPO_ARREGLO,
            'valor' => 'CUP | USD||EUR|',
        ]);

        $service = app(SetupConfigService::class);

        $this->assertSame(15, $service->get('cantidad_demo'));
        $this->assertSame('350.25', $service->get('tasa_demo'));
        $this->assertSame(
            'Empresa de prueba',
            $service->get('empresa_demo')
        );
        $this->assertSame(
            ['CUP', 'USD', 'EUR'],
            $service->get('monedas_demo')
        );
    }

    public function test_devuelve_valor_predeterminado_si_no_existe(): void
    {
        $service = app(SetupConfigService::class);

        $this->assertNull($service->getConfig('inexistente'));

        $this->assertSame(
            'valor alternativo',
            $service->get('inexistente', 'valor alternativo')
        );
    }

    public function test_las_acciones_crud_invalidan_la_cache(): void
    {
        $original = SetupConfig::create([
            'nombre' => 'Cantidad',
            'item_id' => 'cantidad_demo',
            'tipo' => SetupConfig::TIPO_NUMERO,
            'valor' => '10',
        ]);

        $service = app(SetupConfigService::class);

        // Carga la configuración en caché.
        $this->assertSame(10, $service->get('cantidad_demo'));

        // Crear una nueva configuración debe invalidar la caché.
        app(CreateSetupConfigAction::class)(
            CreateSetupConfigDTO::fromArray([
                'nombre' => 'Empresa',
                'item_id' => 'empresa_demo',
                'descripcion' => null,
                'tipo' => SetupConfig::TIPO_TEXTO,
                'valor' => 'Empresa de prueba',
                'valor_inicial' => null,
                'valor_minimo' => null,
                'valor_maximo' => null,
            ])
        );

        $this->assertSame(
            'Empresa de prueba',
            $service->get('empresa_demo')
        );

        // Actualizar debe refrescar el valor almacenado en caché.
        app(UpdateSetupConfigAction::class)(
            UpdateSetupConfigDTO::fromArray([
                'id' => $original->id,
                'nombre' => 'Cantidad',
                'item_id' => 'cantidad_demo',
                'descripcion' => null,
                'tipo' => SetupConfig::TIPO_NUMERO,
                'valor' => '25',
                'valor_inicial' => null,
                'valor_minimo' => null,
                'valor_maximo' => null,
            ])
        );

        $this->assertSame(25, $service->get('cantidad_demo'));

        // Eliminar debe retirar la configuración de la caché.
        app(DeleteSetupConfigAction::class)(
            DeleteSetupConfigDTO::fromArray([
                'id' => $original->id,
            ])
        );

        $this->assertNull($service->getConfig('cantidad_demo'));
    }
}
