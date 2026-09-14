<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('movimientos_inventarios', function (Blueprint $table) {
            // Relaciones antiguas
            $table->dropForeign(['inventario_id']);
            $table->dropForeign(['producto_id']);
            $table->dropForeign(['moneda_id']);
            $table->dropForeign(['tarjeta_magnetica_id']);
            $table->dropForeign(['origen']);
            $table->dropForeign(['destino']);

            // Estructura antigua
            $table->dropColumn([
                'inventario_id',
                'producto_id',
                'moneda_id',
                'tarjeta_magnetica_id',
                'origen',
                'destino',
                'tipo',
                'cantidad',
                'costo',
                'precio',
                'tasa_cambio',
                'descuento',
            ]);
        });

        Schema::table('movimientos_inventarios', function (Blueprint $table) {
            $table->string('tipo', 20)
                ->after('id');

            $table->string('estado', 20)
                ->default('borrador')
                ->after('tipo');

            $table->foreignId('origen_local_id')
                ->nullable()
                ->after('estado')
                ->constrained('locales')
                ->restrictOnDelete();

            $table->foreignId('destino_local_id')
                ->nullable()
                ->after('origen_local_id')
                ->constrained('locales')
                ->restrictOnDelete();

            $table->foreignId('local_id')
                ->nullable()
                ->after('destino_local_id')
                ->constrained('locales')
                ->restrictOnDelete();

            $table->timestamp('fecha')
                ->after('local_id');

            $table->text('observacion')
                ->nullable()
                ->after('fecha');

            $table->index(['tipo', 'estado']);
            $table->index('fecha');
        });
    }

    public function down(): void
    {
        Schema::table('movimientos_inventarios', function (Blueprint $table) {
            $table->dropForeign(['origen_local_id']);
            $table->dropForeign(['destino_local_id']);
            $table->dropForeign(['local_id']);

            $table->dropColumn([
                'tipo',
                'estado',
                'origen_local_id',
                'destino_local_id',
                'local_id',
                'fecha',
                'observacion',
            ]);
        });

        Schema::table('movimientos_inventarios', function (Blueprint $table) {
            $table->foreignId('inventario_id')
                ->nullable()
                ->constrained('inventarios')
                ->nullOnDelete();

            $table->foreignId('producto_id')
                ->nullable()
                ->constrained('productos')
                ->nullOnDelete();

            $table->foreignId('moneda_id')
                ->nullable()
                ->constrained('monedas')
                ->nullOnDelete();

            $table->foreignId('tarjeta_magnetica_id')
                ->nullable()
                ->constrained('tarjetas_magneticas')
                ->nullOnDelete();

            $table->foreignId('origen')
                ->nullable()
                ->constrained('locales')
                ->nullOnDelete();

            $table->foreignId('destino')
                ->nullable()
                ->constrained('locales')
                ->nullOnDelete();

            $table->enum('tipo', ['venta', 'inventario'])
                ->default('inventario');

            $table->decimal('cantidad');
            $table->decimal('costo', 12, 2);
            $table->decimal('precio', 12, 2);
            $table->decimal('tasa_cambio', 18, 6)
                ->default(1);
            $table->decimal('descuento');
        });
    }
};