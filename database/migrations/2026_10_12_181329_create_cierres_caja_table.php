<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cierres_caja', function (Blueprint $table) {
            $table->id();

            $table->foreignId('local_id')
                ->constrained('locales')
                ->restrictOnDelete();

            $table->foreignId('caja_id')
                ->constrained('cajas')
                ->restrictOnDelete();

            $table->foreignId('periodo_contable_id')
                ->constrained('periodos_contables')
                ->restrictOnDelete();

            $table->foreignId('operador_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->date('fecha');

            $table->timestamps();

            $table->unique(
                ['caja_id', 'fecha'],
                'cierres_caja_caja_fecha_unique'
            );

            $table->index(
                ['local_id', 'fecha'],
                'cierres_caja_local_fecha_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cierres_caja');
    }
};
