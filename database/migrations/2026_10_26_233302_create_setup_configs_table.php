
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('setup_configs', function (Blueprint $table) {
            $table->id();

            $table->string('nombre', 150);
            $table->string('item_id', 150)->unique();

            $table->string('descripcion')->nullable();

            // 1 = Numérico, 2 = Texto, 3 = Arreglo.
            $table->unsignedTinyInteger('tipo')->default(1);

            // Conserva el formato heredado, incluidos los valores separados por "|".
            $table->text('valor');

            // Pueden contener números, texto, fechas o configuraciones compuestas.
            $table->text('valor_inicial')->nullable();
            $table->text('valor_minimo')->nullable();
            $table->text('valor_maximo')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('setup_configs');
    }
};
