<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cierre_diario_desgloses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('local_id')->constrained('locales');
            $table->foreignId('periodo_contable_id')->constrained('periodos_contables');
            $table->foreignId('operador_id')->constrained('users');
            $table->decimal('monto_inicial_definido', 12, 2);
            $table->decimal('monto_desglosado_total', 12, 2);
            $table->date('fecha');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cierre_diario_desgloses');
    }
};