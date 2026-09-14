<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('existencias', function (Blueprint $table) {
            $table->id();

            $table->foreignId('producto_id')
                ->constrained('productos')
                ->restrictOnDelete();

            $table->foreignId('local_id')
                ->constrained('locales')
                ->restrictOnDelete();

            $table->decimal('cantidad_base', 20, 10)
                ->default(0);

            $table->timestamps();

            $table->unique([
                'producto_id',
                'local_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('existencias');
    }
};