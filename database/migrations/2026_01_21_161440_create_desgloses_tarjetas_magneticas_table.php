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
        Schema::create('desgloses_tarjetas_magneticas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tarjeta_id')
                  ->constrained("tarjetas_magneticas")
                  ->restrictOnDelete();

            $table->decimal('precio', 12, 2);
            $table->date('fecha');

            $table->morphs('desglosable', 'desgloses_tarjetas_able_id_able_type_index');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('desgloses_tarjetas_magneticas');
    }
};
