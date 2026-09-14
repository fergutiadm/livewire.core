<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trazas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // Usuario que hizo la acción

            $table->morphs('trazable'); // trazable_id + trazable_type

            $table->string('operacion', 20); // create, update, delete, restore
            $table->json('data')->nullable(); // Datos de la traza, puede ser vacío
            $table->string('ip_str', 45)->default('0.0.0.0'); // IP del usuario, IPv6 ok
            // $table->date("fecha");
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trazas');
    }
};
