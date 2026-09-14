<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unidades_medida', function (Blueprint $table) {
            $table->id();

            $table->foreignId('unidad_dimension_id')
                ->nullable()
                ->constrained('unidad_dimensiones')
                ->restrictOnDelete();

            $table->string('codigo', 30)->unique();
            $table->string('nombre', 100);
            $table->string('abreviatura', 20)->nullable();

            /*
             * estandar:
             *   Tiene dimensión y factor_base universal.
             *
             * operativa:
             *   Su equivalencia depende del producto.
             */
            $table->string('tipo', 20)->default('estandar');

            /*
             * Factor respecto a la unidad canónica
             * de su dimensión.
             *
             * Ejemplo:
             * kg = 1
             * g  = 0.001
             * m  = 1
             * cm = 0.01
             *
             * Para unidades operativas:
             * saco = null
             * caja = null
             */
            $table->decimal('factor_base', 20, 10)->nullable();

            $table->boolean('es_base_dimension')->default(false);
            $table->boolean('activo')->default(true);

            $table->timestamps();

            $table->index(['unidad_dimension_id', 'tipo']);
            $table->index(['activo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unidades_medida');
    }
};