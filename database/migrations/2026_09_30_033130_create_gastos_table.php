<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gastos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('local_id')
                ->constrained('locales')
                ->restrictOnDelete();

            $table->foreignId('tipo_gasto_id')
                ->constrained('tipos_gastos')
                ->restrictOnDelete();

            $table->foreignId('periodo_contable_id')
                ->constrained('periodos_contables')
                ->restrictOnDelete();

            /*
             * Persona asociada al gasto.
             *
             * NO es el usuario que registra el gasto.
             * operador_id representa al operador.
             *
             * Dejamos estas relaciones sin FK por ahora porque
             * todavía debemos cerrar la correspondencia Laravel
             * de Especialista/Responsable del sistema legado.
             */
            $table->unsignedBigInteger('especialista_id')->nullable();
            $table->unsignedBigInteger('responsable_id')->nullable();

            /*
             * Usuario/operador que registra el gasto.
             */
            $table->foreignId('operador_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->decimal('cantidad', 20, 10)->default(1);

            /*
             * Valor unitario expresado en la moneda del gasto.
             *
             * Importe derivado:
             * cantidad * valor * tasa_cambio
             */
            $table->decimal('valor', 20, 10)->default(0);

            $table->foreignId('moneda_id')
                ->constrained('monedas')
                ->restrictOnDelete();

            /*
             * Tasa aplicada al momento de registrar el gasto.
             * Se conserva como snapshot histórico.
             */
            $table->decimal('tasa_cambio', 20, 10)->default(1);

            /*
             * Se conserva temporalmente porque existe en el legado
             * y todavía debemos determinar todos sus usos.
             */
            $table->decimal('tasa_cambio_usd', 20, 10)->nullable();

            $table->string('descripcion', 255)->nullable();

            $table->date('fecha');

            /*
             * Revisión administrativa.
             */
            $table->boolean('chequeado')->default(false);
            $table->timestamp('fecha_chequeado')->nullable();

            /*
             * IP técnica registrada al crear el gasto.
             */
            $table->string('ip_var', 45)->nullable();

            /*
             * Soft delete lógico compatible conceptualmente
             * con el legado (deleted_at = 1).
             *
             * Aquí usamos timestamp porque estamos en Laravel.
             */
            $table->softDeletes();

            $table->timestamps();

            $table->index(
                ['local_id', 'fecha'],
                'gastos_local_fecha_index'
            );

            $table->index(
                ['periodo_contable_id', 'fecha'],
                'gastos_periodo_fecha_index'
            );

            $table->index(
                ['tipo_gasto_id', 'fecha'],
                'gastos_tipo_fecha_index'
            );

            $table->index(
                ['especialista_id', 'fecha'],
                'gastos_especialista_fecha_index'
            );

            $table->index(
                ['responsable_id', 'fecha'],
                'gastos_responsable_fecha_index'
            );

            $table->index(
                ['chequeado', 'fecha'],
                'gastos_chequeado_fecha_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gastos');
    }
};