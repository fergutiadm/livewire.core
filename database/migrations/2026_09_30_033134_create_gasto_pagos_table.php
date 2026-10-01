<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gasto_pagos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('gasto_id')
                ->constrained('gastos')
                ->cascadeOnDelete();

            /*
             * Valores iniciales:
             *
             * efectivo
             * tarjeta
             *
             * Se mantiene string por ahora para no crear
             * un catálogo adicional antes de cerrar el dominio.
             */
            $table->string('metodo', 20);

            /*
             * Tarjeta existente del sistema.
             */
            $table->foreignId('tarjeta_magnetica_id')
                ->nullable()
                ->constrained('tarjetas_magneticas')
                ->nullOnDelete();

            /*
             * Snapshot del número de tarjeta cuando sea necesario
             * conservar el dato histórico del legado.
             */
            $table->string('numero_tarjeta', 20)->nullable();

            $table->foreignId('moneda_id')
                ->constrained('monedas')
                ->restrictOnDelete();

            /*
             * Importe efectivamente pagado.
             */
            $table->decimal('cantidad', 20, 10);

            /*
             * Tasa aplicada al pago.
             */
            $table->decimal('tasa_cambio', 20, 10)->default(1);

            $table->timestamp('fecha_pago');

            $table->foreignId('operador_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->string('descripcion', 255)->nullable();

            $table->timestamps();

            $table->index(
                ['gasto_id', 'fecha_pago'],
                'gasto_pagos_gasto_fecha_index'
            );

            $table->index(
                ['metodo', 'fecha_pago'],
                'gasto_pagos_metodo_fecha_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gasto_pagos');
    }
};