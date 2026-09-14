<?php

    use Illuminate\Support\Facades\Route;

    use Illuminate\Support\Facades\Storage;

    use App\Http\Livewire\Admin\Inicio;

    use App\Http\Livewire\Admin\TrazasViewer;

    use App\Http\Livewire\Contabilidad\PeriodosIndex;

    use App\Http\Livewire\Inventario\InventarioIndex;
    use App\Http\Livewire\Ventas\VentasIndex;


    use App\Http\Controllers\BarcodeController;
    use App\Http\Controllers\PlaceholderController;

    use App\Modules\Inventario\Local\Livewire\LocalPage;
    use App\Modules\Inventario\Categoria\Livewire\CategoriaPage;
    use App\Modules\Inventario\Producto\Livewire\ProductoPage;
    use App\Modules\Inventario\Atributo\Livewire\AtributoPage;

    use App\Modules\Finanzas\Moneda\Livewire\MonedaPage;
    use App\Modules\Finanzas\TarjetaMagnetica\Livewire\TarjetaMagneticaPage;

    use App\Modules\Comercial\Cliente\Livewire\ClientePage;
    use App\Modules\Comercial\Gestor\Livewire\GestorPage;

    use App\Modules\Contabilidad\PeriodoContable\Livewire\PeriodoContablePage;

    use App\Modules\Seguridad\User\Livewire\UserPage;

    /*
    |--------------------------------------------------------------------------
    | Web Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register web routes for your application. These
    | routes are loaded by the RouteServiceProvider and all of them will
    | be assigned to the "web" middleware group. Make something great!
    |
    */

    Route::get('/', function () {
        return view('welcome');
    });

    // Route::middleware('auth')->group(function () {
    //     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    //     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    //     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    // });

    Route::middleware([
        'auth:sanctum',
        config('jetstream.auth_session'),
        'verified',
    ])->group(function () {
        Route::get('/dashboard', function () {
            return view('dashboard');
        })->name('dashboard');
    });

    Route::middleware([
        'auth:sanctum',
        config('jetstream.auth_session'),
        'verified',
    ])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', Inicio::class)->name('index');

        Route::get('/trazas', TrazasViewer::class)->name('trazas');

        Route::get(
            '/locales',
            LocalPage::class
        )->name('locales');
        Route::get('/categorias', CategoriaPage::class)->name('categorias');
        Route::get(
            '/productos',
            ProductoPage::class
            )->name('productos');
            Route::get('/atributos', AtributoPage::class)->name('atributos');
        Route::get('/inventarios', function () {
            return view('admin.en-contruccion', ['seccion', 'Inventario']);
        })->name('inventarios');

        Route::get('/clientes', ClientePage::class)->name('clientes');
        Route::get('/gestores', GestorPage::class)->name('gestores');


        Route::get('/monedas', MonedaPage::class)->name('monedas');
        Route::get('/tarjetas_magneticas', TarjetaMagneticaPage::class)->name('tarjetas_magneticas');

        Route::get('/periodos_contables', PeriodoContablePage::class)->name('periodos_contables');

        Route::get('/usuarios', UserPage::class)->name('usuarios');


        Route::get('/configuracion', function () {
            return view('admin.en-contruccion', ['seccion', 'Configuracion']);
        })->name('configuracion');
    });

    // Middleware para proteger rutas internas
    Route::middleware(['auth', 'permission:acceder panel interno'])->group(function() {

        // -------------------
        // Ventas (solo usuarios con permiso)
        // -------------------
        Route::get('/ventas', VentasIndex::class)
            ->name('ventas.index');

        // -------------------
        // Inventario
        // -------------------
        Route::get('/inventario', InventarioIndex::class)
            ->name('inventario.index');

        // -------------------
        // Periodos contables (solo manager/admin)
        // -------------------
        Route::get('/periodos', PeriodosIndex::class)
            ->middleware('permission:ver periodos contables')
            ->name('periodos.index');
    });

    Route::get('/barcode/label/{code}', [BarcodeController::class, 'html'])
        ->name('barcode.label');

    Route::get('/barcode/zpl/{code}', [BarcodeController::class, 'zpl'])
        ->name('barcode.zpl');

    Route::get('/barcode/preview/{code}', [BarcodeController::class, 'preview'])
        ->where('code', '.*')
        ->name('barcode.preview');

    Route::get('/barcode/print', [BarcodeController::class, 'print'])
        ->name('barcode.print');

    Route::get('/placeholder/categoria/{id}', [PlaceholderController::class, 'categoria'])
        ->name('placeholder.categoria');

    Route::get('/placeholder/producto/{id}', [PlaceholderController::class, 'producto'])
        ->name('placeholder.producto');

        // Route::get('/debug-preview/{filename}', function ($filename) {
        //     $path = storage_path('app/public/livewire-tmp/' . $filename);


        //     abort_unless(file_exists($path), 404);

        //     $body = file_get_contents($path);

        //     file_put_contents(
        //         storage_path('logs/response-debug.log'),
        //         'BODY LEN=' . strlen($body) . PHP_EOL .
        //         'BODY HEX START=' . bin2hex(substr($body, 0, 8)) . PHP_EOL .
        //         'BODY HEX END=' . bin2hex(substr($body, -8)) . PHP_EOL .
        //         'OB LEVEL=' . ob_get_level() . PHP_EOL .
        //         'OB LENGTH=' . ob_get_length() . PHP_EOL .
        //         'OB STATUS=' . print_r(ob_get_status(true), true) . PHP_EOL .
        //         PHP_EOL,
        //         FILE_APPEND
        //     );

        //     return new \Symfony\Component\HttpFoundation\Response(
        //         $body,
        //         200,
        //         [
        //             'Content-Type' => 'image/jpeg',
        //         ]
        //     );


        //     });
/**
<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

use App\Http\Livewire\Admin\Inicio;
use App\Http\Livewire\Admin\TrazasViewer;
use App\Http\Livewire\Contabilidad\PeriodosIndex;
use App\Http\Livewire\Inventario\InventarioIndex;
use App\Http\Livewire\Ventas\VentasIndex;

use App\Http\Controllers\BarcodeController;
use App\Http\Controllers\PlaceholderController;

// Módulo: Inventario
use App\Modules\Inventario\Producto\Livewire\ProductoPage;
use App\Modules\Inventario\Categoria\Livewire\CategoriaPage;
use App\Modules\Inventario\Local\Livewire\LocalPage;
use App\Modules\Inventario\Atributo\Livewire\AtributoPage;

// Módulo: Comercial
use App\Modules\Comercial\Cliente\Livewire\ClientePage;
use App\Modules\Comercial\Gestor\Livewire\GestorPage;

// Módulo: Finanzas
use App\Modules\Finanzas\Moneda\Livewire\MonedaPage;
use App\Modules\Finanzas\TarjetaMagnetica\Livewire\TarjetaMagneticaPage;

// Módulo: Contabilidad
use App\Modules\Contabilidad\PeriodoContable\Livewire\PeriodoContablePage;

// Módulo: Seguridad
use App\Modules\Seguridad\User\Livewire\UserPage;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
* /

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])
->prefix('admin')
->name('admin.')
->group(function () {
    Route::get('/', Inicio::class)->name('index');

    Route::get('/trazas', TrazasViewer::class)->name('trazas');

    Route::get('/locales', LocalPage::class)->name('locales');

    Route::get('/monedas', MonedaPage::class)->name('monedas');

    Route::get('/tarjetas_magneticas', TarjetaMagneticaPage::class)->name('tarjetas_magneticas');

    Route::get('/categorias', CategoriaPage::class)->name('categorias');

    Route::get('/productos', ProductoPage::class)->name('productos');

    Route::get('/atributos', AtributoPage::class)->name('atributos');

    Route::get('/usuarios', UserPage::class)->name('usuarios');

    Route::get('/clientes', ClientePage::class)->name('clientes');

    Route::get('/gestores', GestorPage::class)->name('gestores');

    Route::get('/inventarios', function () {
        return view('admin.en-contruccion', ['seccion', 'Inventario']);
    })->name('inventarios');

    Route::get('/periodos_contables', PeriodoContablePage::class)->name('periodos_contables');

    Route::get('/configuracion', function () {
        return view('admin.en-contruccion', ['seccion', 'Configuracion']);
    })->name('configuracion');
});

// Middleware para proteger rutas internas
Route::middleware(['auth', 'permission:acceder panel interno'])->group(function() {

    // -------------------
    // Ventas (solo usuarios con permiso)
    // -------------------
    Route::get('/ventas', VentasIndex::class)
        ->name('ventas.index');

    // -------------------
    // Inventario
    // -------------------
    Route::get('/inventario', InventarioIndex::class)
        ->name('inventario.index');

    // -------------------
    // Periodos contables (solo manager/admin)
    // -------------------
    Route::get('/periodos', PeriodosIndex::class)
        ->middleware('permission:ver periodos contables')
        ->name('periodos.index');
});

Route::get('/barcode/label/{code}', [BarcodeController::class, 'html'])
    ->name('barcode.label');

Route::get('/barcode/zpl/{code}', [BarcodeController::class, 'zpl'])
    ->name('barcode.zpl');

Route::get('/barcode/preview/{code}', [BarcodeController::class, 'preview'])
    ->where('code', '.*')
    ->name('barcode.preview');

Route::get('/barcode/print', [BarcodeController::class, 'print'])
    ->name('barcode.print');

Route::get('/placeholder/categoria/{id}', [PlaceholderController::class, 'categoria'])
    ->name('placeholder.categoria');

Route::get('/placeholder/producto/{id}', [PlaceholderController::class, 'producto'])
    ->name('placeholder.producto');

*/
