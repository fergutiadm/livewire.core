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
        Schema::create('atributables_valores_movimientos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('atributo_valor_id')
                ->constrained('atributos_valores')
                ->cascadeOnDelete();

            $table->morphs('atributable', 'attr_valores_able_id_able_type_index');

            $table->foreignId('moneda_id')
                  ->nullable()
                  ->constrained("monedas")
                  ->nullOnDelete();

            $table->foreignId('tarjeta_magnetica_id')
                  ->nullable()
                  ->constrained("tarjetas_magneticas")
                  ->nullOnDelete();

            $table->decimal('tasa_cambio', 18, 6)->default(1);

            $table->decimal("cantidad");
            $table->decimal('costo', 12, 2);
            $table->decimal('precio', 12, 2);

            $table->timestamps();

            $table->unique([
                'atributo_valor_id',
                'atributable_id',
                'atributable_type'
            ], "attr_valores_attr_valor_mov_id_able_id_able_type_index");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('atributables_valores_movimientos');
    }
};
