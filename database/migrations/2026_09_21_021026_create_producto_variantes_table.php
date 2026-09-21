<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('producto_variantes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('producto_id')
                ->constrained('productos');

            $table->string('clave_combinacion', 255);

            $table->string('nombre')
                ->nullable();

            $table->boolean('activo')
                ->default(true);

            $table->timestamps();

            /*
             * Una combinación concreta solo puede existir
             * una vez dentro del mismo producto.
             */
            $table->unique(
                ['producto_id', 'clave_combinacion'],
                'producto_variantes_producto_clave_unique'
            );

            $table->index(
                ['producto_id', 'activo'],
                'producto_variantes_producto_activo_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('producto_variantes');
    }
};