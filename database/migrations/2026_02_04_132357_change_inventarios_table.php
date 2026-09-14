<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('inventarios', function (Blueprint $table) {

            $table->foreignId('local_id')
                ->after('user_id')
                ->constrained('locales')
                ->cascadeOnDelete();

            $table->foreignId('periodo_contable_id')
                ->nullable()
                ->after('local_id')
                ->constrained('periodos_contables')
                ->nullOnDelete();

            $table->string('tipo')
                ->after('descripcion');

            $table->string('estado')
                ->default('borrador')
                ->after('tipo');

            $table->foreignId('moneda_base_id')
                ->nullable()
                ->after('estado')
                ->constrained('monedas')
                ->nullOnDelete();

        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventarios', function (Blueprint $table) {

            // 🔑 Eliminar foreign keys primero
            $table->dropForeign(['local_id']);
            $table->dropForeign(['periodo_contable_id']);
            $table->dropForeign(['moneda_base_id']);

            // 🧱 Luego eliminar columnas
            $table->dropColumn([
                'local_id',
                'periodo_contable_id',
                'tipo',
                'estado',
                'moneda_base_id',
            ]);

        });
    }
};
