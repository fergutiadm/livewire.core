<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cierres_caja_moneda_detalles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cierre_caja_moneda_id')
                ->constrained('cierres_caja_monedas')
                ->cascadeOnDelete();

            $table->foreignId('denominacion_id')
                ->constrained('denominaciones')
                ->restrictOnDelete();

            // Instantánea del valor aplicado al cierre.
            $table->decimal('valor_denominacion', 20, 2);

            $table->unsignedInteger('cantidad_billetes')->default(0);

            $table->decimal('subtotal', 20, 2);

            $table->timestamps();

            $table->unique(
                ['cierre_caja_moneda_id', 'denominacion_id'],
                'cierres_moneda_detalles_denominacion_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cierres_caja_moneda_detalles');
    }
};
