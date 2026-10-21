<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consolidaciones_diarias', function (Blueprint $table) {
            $table->id();

            $table->foreignId('local_id')
                ->constrained('locales')
                ->restrictOnDelete();

            $table->foreignId('periodo_contable_id')
                ->constrained('periodos_contables')
                ->restrictOnDelete();

            $table->foreignId('operador_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->date('fecha');

            $table->decimal('monto_total', 20, 2)->default(0);

            $table->string('estado', 20)->default('borrador');

            $table->timestamps();

            $table->unique(
                ['local_id', 'fecha'],
                'consolidaciones_local_fecha_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consolidaciones_diarias');
    }
};
