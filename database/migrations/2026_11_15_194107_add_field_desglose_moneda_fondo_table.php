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
        Schema::table('moneda_fondos', function (Blueprint $table) {
            $table->json('desglose')->after('tasa_cambio')->nullable(); //->default('{}');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('moneda_fondos', function (Blueprint $table) {
            $table->dropColumn([
                'desglose',
            ]);
        });
    }
};
