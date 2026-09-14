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
        Schema::create('trazas_monedas', function (Blueprint $table) {
            $table->id();

            $table->foreignId("moneda_id")
                  ->constrained("monedas")
                  ->restrictOnDelete();

            $table->decimal('tasa_cambio', 18, 6)->default(1);
            $table->decimal('tasa_cambio_anterior', 18, 6)->default(1);
            $table->date("fecha");

            $table->morphs('trazable', 'trazas_monedas_able_id_able_type_index');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trazas_monedas');
    }
};
