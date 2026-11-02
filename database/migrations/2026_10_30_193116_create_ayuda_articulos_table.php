
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ayuda_articulos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('categoria_id')
                ->constrained('ayuda_categorias')
                ->restrictOnDelete();

            $table->string('titulo', 200);

            $table->string('slug', 220)->unique();

            $table->text('resumen')->nullable();

            $table->longText('contenido');

            $table->string('contexto', 160)->nullable();

            $table->integer('orden_visual')->default(0);

            $table->boolean('publicado')->default(false);

            $table->timestamps();

            $table->index([
                'categoria_id',
                'publicado',
                'orden_visual',
            ], 'ayuda_articulos_publicacion_idx');

            $table->index('contexto');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ayuda_articulos');
    }
};
