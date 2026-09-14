<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('atributables_valores', function (Blueprint $table) {
            $table->id();

            $table->integer('orden_visual')->unsigned()->nullable();

            $table->foreignId('atributo_valor_id')
                ->constrained('atributos_valores')
                ->cascadeOnDelete();

            $table->morphs('atributable', 'attr_valores_able_id_able_type_index');

            $table->timestamps();

            $table->unique([
                'atributo_valor_id',
                'atributable_id',
                'atributable_type'
            ], "attr_valores_attr_valor_id_able_id_able_type_index");
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('atributables_valores');
    }
};
