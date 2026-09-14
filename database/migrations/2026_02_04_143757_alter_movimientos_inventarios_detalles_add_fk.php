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
        Schema::table('movimientos_inventarios_detalles', function (Blueprint $table) {

            $table->foreign('movimiento_inventario_id','mid_mov_fk')
                ->references('id')
                ->on('movimientos_inventarios')
                ->cascadeOnDelete();

            $table->foreign('producto_id','mid_prod_fk')
                ->references('id')
                ->on('productos')
                ->cascadeOnDelete();

            $table->foreign('atributo_valor_id','mid_attr_fk')
                ->references('id')
                ->on('atributos_valores')
                ->cascadeOnDelete();

        });
    }



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movimientos_inventarios_detalles', function (Blueprint $table) {

            $table->dropForeign(['movimiento_inventario_id']);
            $table->dropForeign(['producto_id']);
            $table->dropForeign(['atributo_valor_id']);

        });
    }

};
