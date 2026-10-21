<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
         * ============================================================
         * 1. REDISEÑO DE gasto_pagos
         * ============================================================
         */

        // El pago ya no se elimina automáticamente al eliminar el gasto.
        Schema::table('gasto_pagos', function (Blueprint $table) {
            $table->dropForeign('gasto_pagos_gasto_id_foreign');
        });

        Schema::table('gasto_pagos', function (Blueprint $table) {
            $table->foreign('gasto_id')
                ->references('id')
                ->on('gastos')
                ->restrictOnDelete();
        });

        // Estado del pago.
        Schema::table('gasto_pagos', function (Blueprint $table) {
            $table->string('estado', 20)
                ->default('draft')
                ->after('gasto_id');

            $table->index(
                ['estado', 'fecha_pago'],
                'gasto_pagos_estado_fecha_index'
            );
        });

        /*
         * Eliminar índice que dependía de metodo.
         */
        Schema::table('gasto_pagos', function (Blueprint $table) {
            $table->dropIndex('gasto_pagos_metodo_fecha_index');
        });

        /*
         * Eliminar las FK de las columnas que pasan a
         * gasto_pago_desgloses.
         */
        Schema::table('gasto_pagos', function (Blueprint $table) {
            $table->dropForeign('gasto_pagos_moneda_id_foreign');
            $table->dropForeign('gasto_pagos_tarjeta_magnetica_id_foreign');
        });

        /*
         * Eliminar columnas que dejan de pertenecer al encabezado
         * del pago.
         */
        Schema::table('gasto_pagos', function (Blueprint $table) {
            $table->dropColumn([
                'metodo',
                'tarjeta_magnetica_id',
                'numero_tarjeta',
                'moneda_id',
                'cantidad',
                'tasa_cambio',
            ]);
        });

        /*
         * ============================================================
         * 2. CREAR gasto_pago_desgloses
         * ============================================================
         */

        Schema::create('gasto_pago_desgloses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('gasto_pago_id')
                ->constrained('gasto_pagos')
                ->cascadeOnDelete();

            $table->foreignId('moneda_id')
                ->constrained('monedas')
                ->restrictOnDelete();

            $table->decimal('cantidad', 20, 10);

            /*
             * Tasa histórica utilizada en el momento del pago.
             *
             * importe CUP = cantidad * tasa_cambio
             */
            $table->decimal('tasa_cambio', 20, 10)
                ->default(1);

            /*
             * Valores permitidos actualmente:
             * efectivo
             * tarjeta
             */
            $table->string('metodo', 20);

            /*
             * Obligatoria cuando metodo = tarjeta.
             */
            $table->foreignId('tarjeta_magnetica_id')
                ->nullable()
                ->constrained('tarjetas_magneticas')
                ->nullOnDelete();

            /*
             * Snapshot histórico del número de tarjeta.
             */
            $table->string('numero_tarjeta', 20)
                ->nullable();

            $table->timestamps();

            $table->index(
                ['gasto_pago_id'],
                'gasto_pago_desgloses_pago_index'
            );

            $table->index(
                ['moneda_id'],
                'gasto_pago_desgloses_moneda_index'
            );

            $table->index(
                ['metodo'],
                'gasto_pago_desgloses_metodo_index'
            );

            $table->index(
                ['tarjeta_magnetica_id'],
                'gasto_pago_desgloses_tarjeta_index'
            );
        });
    }

    public function down(): void
    {
        /*
         * ============================================================
         * 1. Eliminar desgloses
         * ============================================================
         */

        Schema::dropIfExists('gasto_pago_desgloses');

        /*
         * ============================================================
         * 2. Restaurar estructura anterior de gasto_pagos
         * ============================================================
         */

        Schema::table('gasto_pagos', function (Blueprint $table) {
            $table->dropIndex('gasto_pagos_estado_fecha_index');

            $table->dropForeign('gasto_pagos_gasto_id_foreign');
        });

        Schema::table('gasto_pagos', function (Blueprint $table) {
            $table->dropColumn('estado');

            $table->foreign('gasto_id')
                ->references('id')
                ->on('gastos')
                ->cascadeOnDelete();

            $table->string('metodo', 20)->nullable();
            $table->foreignId('tarjeta_magnetica_id')
                ->nullable()
                ->constrained('tarjetas_magneticas')
                ->nullOnDelete();

            $table->string('numero_tarjeta', 20)->nullable();

            $table->foreignId('moneda_id')
                ->nullable()
                ->constrained('monedas')
                ->restrictOnDelete();

            $table->decimal('cantidad', 20, 10)->nullable();
            $table->decimal('tasa_cambio', 20, 10)
                ->default(1);

            $table->index(
                ['metodo', 'fecha_pago'],
                'gasto_pagos_metodo_fecha_index'
            );
        });
    }
};