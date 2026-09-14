<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gestor_productos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('gestor_id')
                ->constrained('gestores')
                ->restrictOnDelete();

            $table->foreignId('producto_id')
                ->constrained('productos')
                ->cascadeOnDelete();

            $table->decimal('precio', 12, 2);

            $table->boolean('activo')
                ->default(true);

            $table->timestamps();

            $table->softDeletes();

            $table->unique([
                'gestor_id',
                'producto_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gestor_productos');
    }
};