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
        Schema::create('conceptos_moneda_fondo', function (Blueprint $table) {
            $table->id();

            $table->string('nombre', 150);

            $table->string('slug', 160)->unique();

            /**
             * true  = entrada
             * false = salida
             */
            $table->boolean('entrada_salida')
                ->default(true);

            /**
             * Permite que el administrador controle
             * si el concepto puede utilizarse.
             */
            $table->boolean('activo')
                ->default(true);

            $table->timestamps();

            $table->index(
                ['activo', 'entrada_salida'],
                'conceptos_moneda_fondo_activo_entrada_salida_idx'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conceptos_moneda_fondo');
    }
};