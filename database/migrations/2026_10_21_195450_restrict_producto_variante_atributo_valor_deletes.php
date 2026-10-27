<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('producto_variantes_atributos_valores', function (Blueprint $table) {
            $table->dropForeign('pvav_atributo_valor_fk');
        });

        Schema::table('producto_variantes_atributos_valores', function (Blueprint $table) {
            $table->foreign('atributo_valor_id', 'pvav_atributo_valor_fk')
                ->references('id')
                ->on('atributos_valores')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('producto_variantes_atributos_valores', function (Blueprint $table) {
            $table->dropForeign('pvav_atributo_valor_fk');
        });

        Schema::table('producto_variantes_atributos_valores', function (Blueprint $table) {
            $table->foreign('atributo_valor_id', 'pvav_atributo_valor_fk')
                ->references('id')
                ->on('atributos_valores')
                ->cascadeOnDelete();
        });
    }
};
