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
        Schema::create('cierres_caja_monedas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cierre_caja_id')
                ->constrained('cierres_caja')
                ->cascadeOnDelete();

            $table->foreignId('moneda_id')
                ->constrained('monedas')
                ->restrictOnDelete();

            $table->decimal('monto_esperado', 20, 2);
            $table->decimal('monto_contado', 20, 2);

            $table->string('estado', 20)->default('confirmado');

            $table->timestamps();

            $table->unique(
                ['cierre_caja_id', 'moneda_id'],
                'cierres_moneda_moneda_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cierres_caja_monedas');
    }
};
