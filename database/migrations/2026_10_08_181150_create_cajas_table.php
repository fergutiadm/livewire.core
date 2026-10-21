<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cajas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('local_id')
                ->constrained('locales')
                ->restrictOnDelete();

            $table->string('codigo', 30);
            $table->string('nombre', 100);
            $table->boolean('activa')->default(true);

            $table->timestamps();

            $table->unique(
                ['local_id', 'codigo'],
                'cajas_local_codigo_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cajas');
    }
};
