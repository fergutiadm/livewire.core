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
        Schema::create('monedas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 8)->unique();
            $table->string('nombre', 64);
            $table->string('simbolo', 8)->nullable();

            $table->boolean('es_principal')->default(false);
            $table->decimal('tasa_cambio', 18, 6)->default(1);

            $table->string('color_bg', 50)->nullable();
            $table->string('color_text', 50)->nullable();

            $table->boolean('activa')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monedas');
    }
};
