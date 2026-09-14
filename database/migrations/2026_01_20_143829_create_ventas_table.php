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
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cliente_id')
                  ->nullable()
                  ->constrained('clientes')
                  ->nullOnDelete();

            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->c();

            $table->foreignId('periodo_contable_id')
                  ->nullable()
                  ->constrained('periodos_contables')
                  ->nullOnDelete();

            $table->foreignId('moneda_id')
                  ->nullable()
                  ->constrained("monedas")
                  ->nullOnDelete();


            $table->foreignId('tarjeta_magnetica_id')
                  ->nullable()
                  ->constrained("tarjetas_magneticas")
                  ->restrictOnDelete();

            $table->decimal("descuento")->nullable()->default(0);
            $table->decimal("mano_obra")->nullable()->default(0);

            $table->date("fecha");
            $table->string("no_vale");
            $table->date("garantia_desde")->nullable();
            $table->date("garantia_hasta")->nullable();
            $table->boolean("pendiente")->default(false);

            $table->boolean("al_por_mayor")->nullable()->default(0);
            $table->json("editada")->nullable();
            $table->enum("tipo", ['producto', 'servicio'])->default('producto');
            $table->string("descripcion");

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
