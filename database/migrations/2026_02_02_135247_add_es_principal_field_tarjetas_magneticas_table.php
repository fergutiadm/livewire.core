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
        Schema::table('tarjetas_magneticas', function (Blueprint $table) {
            if(!Schema::hasColumn('tarjetas_magneticas', 'es_principal')){
                $table->string('es_principal', 50)->nullable()->after('color_text');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tarjetas_magneticas', function (Blueprint $table) {
            if(Schema::hasColumn('tarjetas_magneticas', 'es_principal')){
                $table->dropColumn('es_principal');
            }
        });
    }
};
