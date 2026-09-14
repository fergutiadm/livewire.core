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
        Schema::create('ventas_productos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('venta_id')->nullable()->constrained('ventas')->nullOnDelete();

            $table->foreignId('producto_id')->nullable()->constrained('productos')->nullOnDelete();

            $table->foreignId('moneda_id')
                  ->nullable()
                  ->constrained("monedas")
                  ->nullOnDelete();


            $table->foreignId('tarjeta_magnetica_id')
                  ->nullable()
                  ->constrained("tarjetas_magneticas")
                  ->restrictOnDelete();

            $table->decimal("cantidad");
            $table->decimal('costo', 12, 2);
            $table->decimal('precio', 12, 2);
            $table->decimal('tasa_cambio', 18, 6)->default(1);

            $table->decimal("descuento")->nullable()->default(0);
            $table->decimal("mano_obra")->nullable()->default(0);


            $table->enum("tipo", ['producto', 'servicio'])->default('producto');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas_productos');
    }
};
