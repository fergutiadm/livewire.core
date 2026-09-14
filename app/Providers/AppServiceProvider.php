<?php

namespace App\Providers;
#use Illuminate\Pagination\Paginator;

use App\Models\Categoria;
use App\Observers\CategoriaObserver;
use App\Observers\TrazaObserver;
use Illuminate\Support\Facades\View;
use App\Services\MonedaService;
#use App\Services\Settings;

use Illuminate\Support\Facades\Log;

use Illuminate\Support\ServiceProvider;

use Livewire\Livewire;
use App\Core\Livewire\LivewireRegistry;
use App\Core\Livewire\StrictLivewireResolver;

use App\Modules\Inventario\Producto\Livewire\ProductoPage;
use App\Modules\Inventario\Producto\Livewire\ProductoTable;
use App\Modules\Inventario\Producto\Livewire\ProductoForm;
use App\Modules\Inventario\Producto\Livewire\ProductoMediaManager;
use App\Modules\Inventario\Producto\Livewire\ProductoAttributesManager;

use App\Modules\Inventario\Categoria\Livewire\CategoriaPage;
use App\Modules\Inventario\Categoria\Livewire\CategoriaTable;
use App\Modules\Inventario\Categoria\Livewire\CategoriaForm;
use App\Modules\Inventario\Categoria\Livewire\CategoriaMediaManager;
use App\Modules\Inventario\Categoria\Livewire\CategoriaAttributesManager;

use App\Modules\Inventario\Local\Livewire\LocalPage;
use App\Modules\Inventario\Local\Livewire\LocalTable;
use App\Modules\Inventario\Local\Livewire\LocalForm;
use App\Modules\Inventario\Local\Livewire\LocalMediaManager;
use App\Modules\Inventario\Local\Livewire\LocalAttributesManager;

use App\Modules\Finanzas\Moneda\Livewire\MonedaPage;
use App\Modules\Finanzas\Moneda\Livewire\MonedaTable;
use App\Modules\Finanzas\Moneda\Livewire\MonedaForm;
use App\Modules\Finanzas\Moneda\Livewire\MonedaMediaManager;
use App\Modules\Finanzas\Moneda\Livewire\MonedaAttributesManager;

use App\Modules\Finanzas\TarjetaMagnetica\Livewire\TarjetaMagneticaPage;
use App\Modules\Finanzas\TarjetaMagnetica\Livewire\TarjetaMagneticaTable;
use App\Modules\Finanzas\TarjetaMagnetica\Livewire\TarjetaMagneticaForm;
// use App\Modules\Finanzas\TarjetaMagnetica\Livewire\TarjetaMagneticaMediaManager;
// use App\Modules\Finanzas\TarjetaMagnetica\Livewire\TarjetaMagneticaAttributesManager;

use App\Modules\Inventario\Atributo\Livewire\AtributoPage;
use App\Modules\Inventario\Atributo\Livewire\AtributoTable;
use App\Modules\Inventario\Atributo\Livewire\AtributoForm;
// use App\Modules\Inventario\Atributo\Livewire\AtributoMediaManager;
// use App\Modules\Inventario\Atributo\Livewire\AtributoAttributesManager;

use App\Modules\Comercial\Cliente\Livewire\ClientePage;
use App\Modules\Comercial\Cliente\Livewire\ClienteTable;
use App\Modules\Comercial\Cliente\Livewire\ClienteForm;
// use App\Modules\Comercial\Cliente\Livewire\ClienteMediaManager;
// use App\Modules\Comercial\Cliente\Livewire\ClienteAttributesManager;

use App\Modules\Seguridad\User\Livewire\UserPage;
use App\Modules\Seguridad\User\Livewire\UserTable;
use App\Modules\Seguridad\User\Livewire\UserForm;
// use App\Modules\Seguridad\User\Livewire\UserMediaManager;
// use App\Modules\Seguridad\User\Livewire\UserAttributesManager;

use App\Modules\Comercial\Gestor\Livewire\GestorPage;
use App\Modules\Comercial\Gestor\Livewire\GestorTable;
use App\Modules\Comercial\Gestor\Livewire\GestorForm;
// use App\Modules\Comercial\Gestor\Livewire\GestorMediaManager;
// use App\Modules\Comercial\Gestor\Livewire\GestorAttributesManager;

use App\Http\Livewire\Admin\TablaTrazasViewer;

use App\Modules\Contabilidad\PeriodoContable\Livewire\PeriodoContablePage;
use App\Modules\Contabilidad\PeriodoContable\Livewire\PeriodoContableTable;
use App\Modules\Contabilidad\PeriodoContable\Livewire\PeriodoContableForm;
// use App\Modules\Contabilidad\PeriodoContable\Livewire\PeriodoContableMediaManager;
// use App\Modules\Contabilidad\PeriodoContable\Livewire\PeriodoContableAttributesManager;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //$this->app->singleton(Settings::class, fn() => new Settings);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Categoria::observe(CategoriaObserver::class);
        // Compartir monedas con los layouts app y app_admin
        View::composer(['layouts.app', 'layouts.app_admin',], function ($view) {
            $service = app(MonedaService::class);

            $view->with([
                'uiCurrencies' => collect($service->listForDisplay()),
                'primaryCode' => $service->codigoPrincipal(),
            ]);
        });

        $this->registerLivewireComponents();

        // app()->terminating(function () {
        //     Log::info('LARAVEL - terminating', [
        //         'time' => microtime(true),
        //     ]);
        // });

        // app()->terminating(function () {
        //     Log::info('LARAVEL - terminating', [
        //         'request_id' => request()->header('X-Request-ID'),
        //         'uri' => request()->getRequestUri(),
        //         'method' => request()->method(),
        //         'time' => microtime(true),
        //     ]);
        // });

    }

    protected function registerLivewire(string $alias, string $class): void
    {
        LivewireRegistry::register($alias, $class);

        Livewire::component($alias, $class);
    }

    protected function registerLivewireComponents(): void
    {
        $this->registerLivewire('admin.trazas.tabla', TablaTrazasViewer::class);

        $this->registerLivewire('inventario.local.page', LocalPage::class);
        $this->registerLivewire('inventario.local.form', LocalForm::class);
        $this->registerLivewire('inventario.local.table', LocalTable::class);
        $this->registerLivewire('inventario.local.media-manager', LocalMediaManager::class);
        $this->registerLivewire('inventario.local.attributes-manager', LocalAttributesManager::class);

        $this->registerLivewire('inventario.categoria.page', CategoriaPage::class);
        $this->registerLivewire('inventario.categoria.form', CategoriaForm::class);
        $this->registerLivewire('inventario.categoria.table', CategoriaTable::class);
        $this->registerLivewire('inventario.categoria.media-manager', CategoriaMediaManager::class);
        $this->registerLivewire('inventario.categoria.attributes-manager', CategoriaAttributesManager::class);

        $this->registerLivewire('inventario.producto.page', ProductoPage::class);
        $this->registerLivewire('inventario.producto.form', ProductoForm::class);
        $this->registerLivewire('inventario.producto.table', ProductoTable::class);
        $this->registerLivewire('inventario.producto.media-manager', ProductoMediaManager::class);
        $this->registerLivewire('inventario.producto.attributes-manager', ProductoAttributesManager::class);

        $this->registerLivewire('inventario.atributo.page', AtributoPage::class);
        $this->registerLivewire('inventario.atributo.form', AtributoForm::class);
        $this->registerLivewire('inventario.atributo.table', AtributoTable::class);
        // $this->registerLivewire('inventario.atributo.media-manager', AtributoMediaManager::class);
        // $this->registerLivewire('inventario.atributo.attributes-manager', AtributoAttributesManager::class);

        $this->registerLivewire('finanzas.moneda.page', MonedaPage::class);
        $this->registerLivewire('finanzas.moneda.form', MonedaForm::class);
        $this->registerLivewire('finanzas.moneda.table', MonedaTable::class);
        // $this->registerLivewire('finanzas.moneda.media-manager', MonedaMediaManager::class);
        // $this->registerLivewire('finanzas.moneda.attributes-manager', MonedaAttributesManager::class);

        $this->registerLivewire('finanzas.tarjeta-magnetica.page', TarjetaMagneticaPage::class);
        $this->registerLivewire('finanzas.tarjeta-magnetica.form', TarjetaMagneticaForm::class);
        $this->registerLivewire('finanzas.tarjeta-magnetica.table', TarjetaMagneticaTable::class);
        // $this->registerLivewire('finanzas.tarjeta-magnetica.media-manager', TarjetaMagneticaMediaManager::class);
        // $this->registerLivewire('finanzas.tarjeta-magnetica.attributes-manager', TarjetaMagneticaAttributesManager::class);

        $this->registerLivewire('comercial.cliente.page', ClientePage::class);
        $this->registerLivewire('comercial.cliente.form', ClienteForm::class);
        $this->registerLivewire('comercial.cliente.table', ClienteTable::class);
        // $this->registerLivewire('comercial.cliente.media-manager', ClienteMediaManager::class);
        // $this->registerLivewire('comercial.cliente.attributes-manager', ClienteAttributesManager::class);

        $this->registerLivewire('comercial.gestor.page', GestorPage::class);
        $this->registerLivewire('comercial.gestor.form', GestorForm::class);
        $this->registerLivewire('comercial.gestor.table', GestorTable::class);
        // $this->registerLivewire('comercial.gestor.media-manager', GestorMediaManager::class);
        // $this->registerLivewire('comercial.gestor.attributes-manager', GestorAttributesManager::class);

        $this->registerLivewire('seguridad.user.page', UserPage::class);
        $this->registerLivewire('seguridad.user.form', UserForm::class);
        $this->registerLivewire('seguridad.user.table', UserTable::class);
        // $this->registerLivewire('seguridad.user.media-manager', UserMediaManager::class);
        // $this->registerLivewire('seguridad.user.attributes-manager', UserAttributesManager::class);

        $this->registerLivewire('contabilidad.periodo-contable.page', PeriodoContablePage::class);
        $this->registerLivewire('contabilidad.periodo-contable.form', PeriodoContableForm::class);
        $this->registerLivewire('contabilidad.periodo-contable.table', PeriodoContableTable::class);
        // $this->registerLivewire('contabilidad.periodo-contable.media-manager', PeriodoContableMediaManager::class);
        // $this->registerLivewire('contabilidad.periodo-contable.attributes-manager', PeriodoContableAttributesManager::class);
    }
}
