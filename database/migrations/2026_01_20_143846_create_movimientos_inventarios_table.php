<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('movimientos_inventarios', function (Blueprint $table) {
            $table->id();

            $table->foreignId('inventario_id')->nullable()->constrained('inventarios')->nullOnDelete();

            $table->foreignId('producto_id')->nullable()->constrained('productos')->nullOnDelete();

            $table->foreignId('moneda_id')
                  ->nullable()
                  ->constrained("monedas")
                  ->nullOnDelete();


            $table->foreignId('tarjeta_magnetica_id')
                  ->nullable()
                  ->constrained("tarjetas_magneticas")
                  ->nullOnDelete();

            $table->foreignId('origen')->nullable()->constrained('locales')->nullOnDelete();
            $table->foreignId('destino')->nullable()->constrained('locales')->nullOnDelete();

            $table->enum('tipo', ['venta','inventario'])->default('inventario');

            $table->decimal("cantidad");
            $table->decimal('costo', 12, 2);
            $table->decimal('precio', 12, 2);
            $table->decimal('tasa_cambio', 18, 6)->default(1);
            $table->decimal("descuento");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos_inventarios');
    }
};
