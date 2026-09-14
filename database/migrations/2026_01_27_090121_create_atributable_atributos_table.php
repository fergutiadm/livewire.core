<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('atributables_atributos', function (Blueprint $table) {
            $table->id();

            // Orden visual de los atributos (para drag & drop de cards)
            $table->integer('orden_visual')->unsigned()->nullable();

            // FK al atributo
            $table->foreignId('atributo_id')
                ->constrained('atributos')
                ->cascadeOnDelete();

            // Polimórfico: producto, categoria, etc.
            $table->morphs('atributable', 'attr_able_id_able_type_index');

            $table->timestamps();

            // Unicidad: un atributo no puede repetirse para un mismo modelo
            $table->unique([
                'atributo_id',
                'atributable_id',
                'atributable_type'
            ], 'attr_atributos_attr_id_able_id_able_type_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('atributables_atributos');
    }
};
