<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();

            // Relaciones
            $table->foreignId('local_id')
                ->constrained('locales')
                ->cascadeOnDelete();

            $table->foreignId('producto_id')
                ->constrained('productos')
                ->cascadeOnDelete();

            $table->foreignId('atributo_valor_id')
                ->nullable()
                ->constrained('atributos_valores')
                ->nullOnDelete();

            // Cantidad
            $table->decimal('cantidad', 14, 4)->default(0);

            $table->timestamps();

            // Único por combinación
            $table->unique(['local_id', 'producto_id', 'atributo_valor_id'], 'stock_unique_combo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};

