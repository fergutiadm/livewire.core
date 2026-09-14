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
        Schema::create('movimientos_inventarios_detalles', function (Blueprint $table) {

            $table->id();

            $table->foreignId('movimiento_inventario_id');
            $table->foreignId('producto_id');

            $table->foreignId('atributo_valor_id')
                ->nullable();

            $table->decimal('cantidad',12,3);

            $table->decimal('costo_unitario_base',14,4)
                ->nullable();

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos_inventarios_detalles');
    }
};
