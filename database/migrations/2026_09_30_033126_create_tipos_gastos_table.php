<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipos_gastos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('local_id')
                ->constrained('locales')
                ->restrictOnDelete();

            $table->string('nombre', 50);
            $table->string('descripcion', 200)->nullable();

            // 0 = normal
            // 1 = fijo/recurrente
            $table->boolean('fijo')->default(false);

            $table->boolean('activo')->default(true);

            $table->timestamps();

            $table->unique(
                ['local_id', 'nombre'],
                'tipos_gastos_local_nombre_unique'
            );

            $table->index(
                ['local_id', 'activo'],
                'tipos_gastos_local_activo_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tipos_gastos');
    }
};