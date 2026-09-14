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
        Schema::create('categorias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('local_id')
                  ->nullable()
                  ->constrained('locales')
                  ->nullOnDelete(); // null = categoría global

            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->json('imagenes')->nullable();
            $table->integer('orden_visual')->default(0);
            $table->decimal('porciento_descuento', 5, 2)->default(0);
            $table->timestamps();

            $table->unique(
                ['orden_visual', 'local_id'],
                'categoria_unico_por_local'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categorias');
    }
};
