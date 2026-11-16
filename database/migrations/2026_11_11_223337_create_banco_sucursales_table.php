<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('banco_sucursales', function (Blueprint $table) {
            $table->id();

            $table->string('nombre', 150);

            $table->string('codigo', 150)->nullable();

            $table->foreignId('banco_id')
                ->constrained('bancos')
                ->restrictOnDelete();

            $table->foreignId('municipio_id')
                ->constrained('municipios')
                ->restrictOnDelete();

            $table->string('direccion', 255)->nullable();

            $table->string('telefonos', 255)->nullable();

            $table->boolean('activa')->default(true);

            $table->unique(
                ['banco_id', 'codigo'],
                'banco_sucursal_banco_codigo_unique'
            );

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('banco_sucursales');
    }
};
