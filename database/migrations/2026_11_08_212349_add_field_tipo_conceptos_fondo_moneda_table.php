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
        Schema::table('conceptos_moneda_fondo', function (Blueprint $table) {
            /*
             * estandar:
             *   Aparece en el select de MonedaFondo.
             *
             * operacional:
             *   Se usa desde la interface correspondiente.
             */
            $table->string('tipo', 20)->default('estandar')->after('nombre');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conceptos_moneda_fondo', function (Blueprint $table) {
            $table->dropColumn([
                'tipo',
            ]);
        });
    }
};
