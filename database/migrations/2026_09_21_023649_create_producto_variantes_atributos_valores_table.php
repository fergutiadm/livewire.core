<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('producto_variantes_atributos_valores', function (Blueprint $table) {
            $table->foreignId('producto_variante_id');

            $table->foreignId('atributo_valor_id');

            $table->primary([
                'producto_variante_id',
                'atributo_valor_id',
            ]);

            $table->foreign('producto_variante_id', 'pvav_producto_variante_fk')
                ->references('id')
                ->on('producto_variantes')
                ->cascadeOnDelete();

            $table->foreign('atributo_valor_id', 'pvav_atributo_valor_fk')
                ->references('id')
                ->on('atributos_valores')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('producto_variantes_atributos_valores');
    }
};