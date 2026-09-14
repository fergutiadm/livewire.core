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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('local_id')
                  ->nullable()
                  ->constrained('locales')
                  ->nullOnDelete(); // producto global o por local

            $table->foreignId('categoria_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('moneda_id')
                  ->constrained()
                  ->restrictOnDelete();

            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->json('imagenes')->nullable();

            $table->integer('orden_visual')->default(0);
            $table->decimal('porciento_descuento', 5, 2)->default(0);

            $table->decimal('costo', 12, 2);
            $table->decimal('precio', 12, 2);
            $table->string('codigo')->unique();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
