<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('movimientos_inventarios_detalles', function (Blueprint $table) {
            $table->foreignId('unidad_medida_original_id')
                ->nullable()
                ->after('cantidad');

            $table->decimal('factor_aplicado', 20, 10)
                ->nullable()
                ->after('unidad_medida_original_id');

            $table->decimal('cantidad_base', 20, 10)
                ->nullable()
                ->after('factor_aplicado');

            $table->foreignId('unidad_medida_base_id')
                ->nullable()
                ->after('cantidad_base');

            $table->string('tipo_ajuste', 20)
                ->nullable()
                ->after('costo_unitario_base');

            $table->foreign(
                'unidad_medida_original_id',
                'mov_det_unidad_original_fk'
            )
                ->references('id')
                ->on('unidades_medida')
                ->restrictOnDelete();

            $table->foreign(
                'unidad_medida_base_id',
                'mov_det_unidad_base_fk'
            )
                ->references('id')
                ->on('unidades_medida')
                ->restrictOnDelete();

            $table->index(
                'unidad_medida_original_id',
                'mov_det_unidad_original_idx'
            );

            $table->index(
                'unidad_medida_base_id',
                'mov_det_unidad_base_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::table('movimientos_inventarios_detalles', function (Blueprint $table) {
            $table->dropForeign('mov_det_unidad_original_fk');
            $table->dropForeign('mov_det_unidad_base_fk');

            $table->dropIndex('mov_det_unidad_original_idx');
            $table->dropIndex('mov_det_unidad_base_idx');

            $table->dropColumn([
                'unidad_medida_original_id',
                'factor_aplicado',
                'cantidad_base',
                'unidad_medida_base_id',
                'tipo_ajuste',
            ]);
        });
    }
};