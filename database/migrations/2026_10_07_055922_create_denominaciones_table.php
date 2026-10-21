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
        Schema::create('denominaciones', function (Blueprint $table) {
            $table->id();
            
            // Relación obligatoria con tu tabla de monedas activa
            $table->foreignId('moneda_id')
                  ->constrained('monedas')
                  ->cascadeOnDelete();

            // Guardamos el valor nominal con precisión de centavos (ej: 500.00, 0.50)
            $table->decimal('valor', 12, 2);

            // Control de activación por parte del administrador
            $table->boolean('activa')->default(true);
            
            $table->timestamps();

            // 🔥 REGLA CLAVE: Evita duplicar el mismo valor de billete para una misma moneda
            $table->unique(['moneda_id', 'valor']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('denominaciones');
    }
};
