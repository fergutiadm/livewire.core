<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('periodos_contables', function (Blueprint $table) {
            $table->id();

            $table->string('nombre')->nullable();

            $table->date('fecha_inicio');
            $table->date('fecha_fin');

            $table->boolean('activo')->default(false);
            $table->boolean('cerrado')->default(false);

            $table->foreignId('local_id')
                  ->constrained('locales')
                  ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(
                ['fecha_inicio', 'fecha_fin', 'local_id'],
                'periodo_contable_unico_por_local'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('periodos_contables');
    }
};

