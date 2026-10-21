<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cierre_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cierre_diario_desglose_id')->constrained('cierre_diario_desgloses')->cascadeOnDelete();
            $table->foreignId('denominacion_id')->constrained('denominaciones');
            $table->integer('cantidad_billetes')->default(0);
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cierre_detalles');
    }
};