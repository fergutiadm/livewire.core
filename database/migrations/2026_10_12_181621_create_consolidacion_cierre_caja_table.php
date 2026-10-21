<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consolidacion_cierre_caja', function (Blueprint $table) {
            $table->id();

            $table->foreignId('consolidacion_diaria_id')
                ->constrained('consolidaciones_diarias')
                ->cascadeOnDelete();

            $table->foreignId('cierre_caja_id')
                ->constrained('cierres_caja')
                ->restrictOnDelete();

            $table->timestamps();

            $table->unique(
                'cierre_caja_id',
                'consolidacion_cierre_caja_cierre_unique'
            );

            $table->unique(
                ['consolidacion_diaria_id', 'cierre_caja_id'],
                'consolidacion_cierre_caja_pair_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consolidacion_cierre_caja');
    }
};
