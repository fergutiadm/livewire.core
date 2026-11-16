<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('moneda_fondos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('local_id')
                ->constrained('locales')
                ->restrictOnDelete();

            $table->foreignId('periodo_id')
                ->constrained('periodos_contables')
                ->restrictOnDelete();

            $table->foreignId('moneda_id')
                ->constrained('monedas')
                ->restrictOnDelete();

            $table->foreignId('generado_por_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('concepto_id')
                ->constrained('conceptos_moneda_fondo')
                ->restrictOnDelete();

            $table->string('descripcion', 255);

            $table->decimal('monto', 20, 2);

            $table->decimal('tasa_cambio', 20, 10)
                ->default(1);

            $table->date('fecha');

            $table->timestamps();

            $table->index(
                ['local_id', 'moneda_id', 'fecha'],
                'moneda_fondo_local_moneda_fecha_idx'
            );

            $table->index(
                ['local_id', 'fecha'],
                'moneda_fondo_local_fecha_idx'
            );

            $table->index(
                ['concepto_id', 'fecha'],
                'moneda_fondo_concepto_fecha_idx'
            );

            $table->index(
                ['periodo_id', 'fecha'],
                'moneda_fondo_periodo_fecha_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('moneda_fondos');
    }
};