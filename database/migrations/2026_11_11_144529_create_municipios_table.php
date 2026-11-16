<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('municipios', function (Blueprint $table) {
            $table->id();

            $table->foreignId('provincia_id')
                ->nullable()
                ->constrained('provincias')
                ->restrictOnDelete();

            $table->string('nombre', 150);
            $table->string('slug', 160)->unique();
            $table->boolean('activo')->default(true);

            $table->timestamps();

            $table->index(['provincia_id', 'activo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('municipios');
    }
};