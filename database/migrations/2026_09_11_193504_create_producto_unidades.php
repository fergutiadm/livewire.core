<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('producto_unidades', function (Blueprint $table) {
            $table->id();

            $table->foreignId('producto_id')
                ->constrained('productos')
                ->restrictOnDelete();

            $table->foreignId('unidad_medida_id')
                ->constrained('unidades_medida')
                ->restrictOnDelete();

            /*
             * Factor que convierte esta unidad
             * directamente a la unidad base del producto.
             *
             * Ejemplo:
             *
             * Arroz:
             * saco = 50
             *
             * si la unidad base es kg:
             * 1 saco = 50 kg
             */
            $table->decimal('factor_a_base', 20, 10);

            $table->boolean('activo')->default(true);

            $table->timestamps();

            $table->unique(
                ['producto_id', 'unidad_medida_id'],
                'producto_unidad_unica'
            );

            $table->index(['producto_id', 'activo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('producto_unidades');
    }
};  