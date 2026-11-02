
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ayuda_categorias', function (Blueprint $table) {
            $table->id();

            $table->string('nombre', 150);

            $table->string('slug', 160)->unique();

            $table->text('descripcion')->nullable();

            $table->integer('orden_visual')->default(0);

            $table->boolean('activa')->default(true);

            $table->timestamps();

            $table->index(['activa', 'orden_visual']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ayuda_categorias');
    }
};
