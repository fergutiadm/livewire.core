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
        Schema::create('trazas_tarjetas_magneticas', function (Blueprint $table) {
            $table->id();

            $table->foreignId("tarjeta_magnetica_id")
                  ->constrained("tarjetas_magneticas")
                  ->restrictOnDelete();

            $table->morphs('trazable', 'trazas_tarj_mag_able_id_able_type_index');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trazas_tarjetas_magneticas');
    }
};
